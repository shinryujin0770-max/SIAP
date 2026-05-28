<?php

namespace App\Console\Commands;

use App\Models\Rfk;
use App\Models\Rfk_detail;
use App\Models\RfkV2;
use App\Models\RfkV2Detail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateRfkToRfkV2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rfk:migrate-to-v2 {--dry-run : Run without actually migrating data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate data from RFK (rfk, rfk_detail) to RFK V2 (rfk_v2, rfk_v2_detail)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No data will be migrated');
        }

        $this->info('Starting RFK to RFK V2 migration...');

        // Get all RFK records
        $rfkRecords = DB::table('rfk')->get();
        $this->info("Found {$rfkRecords->count()} RFK records to migrate");

        $rfkV2Count = 0;
        $rfkV2DetailCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            foreach ($rfkRecords as $rfk) {
                try {
                    // Parse month to extract month and year
                    // Assuming month format is like "01", "02", etc. or "01-2024"
                    $month = $rfk->month;
                    $year = date('Y'); // Default to current year

                    // Try to parse month format
                    if (strpos($month, '-') !== false) {
                        // Format: "01-2024"
                        list($monthPart, $yearPart) = explode('-', $month);
                        $month = str_pad($monthPart, 2, '0', STR_PAD_LEFT);
                        $year = $yearPart;
                    } else {
                        // Format: "01" or "1"
                        $month = str_pad($month, 2, '0', STR_PAD_LEFT);
                        // Try to get year from created_at or updated_at
                        if ($rfk->created_at) {
                            $year = Carbon::parse($rfk->created_at)->format('Y');
                        }
                    }

                    // Check if RFK V2 already exists for this OPD/Month/Year
                    $existingRfkV2 = DB::table('rfk_v2')
                        ->where('id_opd', $rfk->id_opd)
                        ->where('month', $month)
                        ->where('year', $year)
                        ->first();

                    if ($existingRfkV2) {
                        $this->warn("RFK V2 already exists for OPD {$rfk->id_opd}, Month {$month}, Year {$year}. Skipping...");
                        continue;
                    }

                    // Create RFK V2 record
                    if (!$dryRun) {
                        $rfkV2Id = DB::table('rfk_v2')->insertGetId([
                            'id_opd' => $rfk->id_opd,
                            'month' => $month,
                            'year' => $year,
                            'status' => $rfk->status,
                            'created_at' => $rfk->created_at ?? now(),
                            'updated_at' => $rfk->updated_at ?? now(),
                        ]);
                    } else {
                        $rfkV2Id = 'DRY_RUN_ID';
                        $this->line("  Would create RFK V2: OPD {$rfk->id_opd}, Month {$month}, Year {$year}");
                    }

                    $rfkV2Count++;

                    // Get OPD info for creating OPD level entry
                    $opd = DB::table('opd')->where('id', $rfk->id_opd)->first();
                    $opdCode = $opd ? ($opd->rfk_code ?? '4.01.0.00.0.00.01.00') : '4.01.0.00.0.00.01.00';
                    $opdName = $opd ? $opd->name : 'OPD';

                    // Create OPD level entry first
                    if (!$dryRun) {
                        $opdDetailId = DB::table('rfk_v2_detail')->insertGetId([
                            'id_rfk' => $rfkV2Id,
                            'parent_id' => null,
                            'type' => 'opd',
                            'code' => $opdCode,
                            'uraian' => $opdName,
                            'year' => $year,
                            'pagu_anggaran' => 0,
                            'bulan_lalu' => 0,
                            'bulan_ini' => 0,
                            'sd_bulan_ini' => 0,
                            'sisa_anggaran' => 0,
                            'angkas' => 0,
                            'angkas_prosentase' => 0,
                            'keuangan' => 0,
                            'fisik' => 0,
                            'fisik_rupiah' => 0,
                            'keterangan' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $opdDetailId = 'DRY_RUN_OPD_ID';
                        $this->line("  Would create OPD detail: Code {$opdCode}");
                    }
                    $rfkV2DetailCount++; // Count OPD detail

                    // Get all RFK details for this RFK
                    $rfkDetails = DB::table('rfk_detail')
                        ->where('id_rfk', $rfk->id)
                        ->orderBy('code')
                        ->get();

                    $this->info("  Processing {$rfkDetails->count()} RFK details for RFK ID {$rfk->id}");

                    // Create a mapping to track hierarchy: code => rfk_v2_detail_id
                    $codeMapping = [$opdCode => $opdDetailId];

                    // Sort details by code length (shortest first) to build hierarchy
                    $sortedDetails = $rfkDetails->sortBy(function($detail) {
                        return strlen($detail->code);
                    });

                    foreach ($sortedDetails as $detail) {
                        try {
                            // Determine type based on code length/pattern
                            $type = $this->determineType($detail->code);

                            // Find parent by matching code prefix
                            $parentId = $this->findParentId($detail->code, $codeMapping, $opdCode);

                            // Create RFK V2 Detail record
                            if (!$dryRun) {
                                $detailId = DB::table('rfk_v2_detail')->insertGetId([
                                    'id_rfk' => $rfkV2Id,
                                    'parent_id' => $parentId,
                                    'type' => $type,
                                    'code' => $detail->code,
                                    'uraian' => $detail->uraian ?? null, // Get uraian from rfk_detail
                                    'year' => $year,
                                    'pagu_anggaran' => $detail->pagu_anggaran ?? 0,
                                    'bulan_lalu' => $detail->bulan_lalu ?? 0,
                                    'bulan_ini' => $detail->bulan_ini ?? 0,
                                    'sd_bulan_ini' => $detail->sd_bulan_ini ?? 0,
                                    'sisa_anggaran' => $detail->sisa_anggaran ?? 0,
                                    'angkas' => $detail->angkas ?? 0,
                                    'angkas_prosentase' => $detail->angkas_prosentase ?? 0,
                                    'keuangan' => $detail->keuangan ?? 0,
                                    'fisik' => $detail->fisik ?? 0,
                                    'fisik_rupiah' => ($detail->fisik ?? 0) * ($detail->pagu_anggaran ?? 0) / 100,
                                    'keterangan' => $detail->keterangan ?? null,
                                    'created_at' => $detail->created_at ?? now(),
                                    'updated_at' => $detail->updated_at ?? now(),
                                ]);
                            } else {
                                $detailId = 'DRY_RUN_DETAIL_ID';
                                $this->line("    Would create {$type} detail: Code {$detail->code}, Parent: " . ($parentId ?: 'OPD'));
                            }

                            $codeMapping[$detail->code] = $detailId;
                            $rfkV2DetailCount++;

                        } catch (\Exception $e) {
                            $errors[] = "Error processing RFK Detail ID {$detail->id}: " . $e->getMessage();
                            $this->error("  Error processing detail ID {$detail->id}: " . $e->getMessage());
                        }
                    }

                } catch (\Exception $e) {
                    $errors[] = "Error processing RFK ID {$rfk->id}: " . $e->getMessage();
                    $this->error("Error processing RFK ID {$rfk->id}: " . $e->getMessage());
                }
            }

            if ($dryRun) {
                DB::rollBack();
                $this->info("\nDRY RUN completed. No data was migrated.");
            } else {
                DB::commit();
                $this->info("\nMigration completed successfully!");
            }

            $this->info("RFK V2 records created: {$rfkV2Count}");
            $this->info("RFK V2 Detail records created: {$rfkV2DetailCount}");

            if (!empty($errors)) {
                $this->warn("\nErrors encountered:");
                foreach ($errors as $error) {
                    $this->warn("  - {$error}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Migration failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Determine type based on code structure
     * Adjust this logic based on your actual code structure
     */
    private function determineType($code)
    {
        // Remove dots and analyze code structure
        $codeParts = explode('.', $code);
        $codeLength = count($codeParts);

        // Example logic - adjust based on your code structure
        // OPD: Usually 2-3 parts, e.g., "4.01.0.00.0.00.01.00"
        // Program: OPD code + additional parts
        // Kegiatan: Program code + additional parts
        // Sub Kegiatan: Kegiatan code + additional parts

        if ($codeLength <= 8) {
            // Likely OPD level
            return 'opd';
        } elseif ($codeLength <= 10) {
            // Likely Program level
            return 'program';
        } elseif ($codeLength <= 12) {
            // Likely Kegiatan level
            return 'kegiatan';
        } else {
            // Likely Sub Kegiatan
            return 'sub_kegiatan';
        }
    }

    /**
     * Find parent ID based on code prefix
     */
    private function findParentId($code, $codeMapping, $opdCode)
    {
        // Try to find parent by code prefix
        // For example, if code is "4.01.0.00.0.00.01.0001.1.01"
        // Parent might be "4.01.0.00.0.00.01.0001.1" (kegiatan)
        // or "4.01.0.00.0.00.01.0001" (program)
        // or "4.01.0.00.0.00.01.00" (OPD)

        $codeParts = explode('.', $code);

        // Try different prefix lengths (longest first to find closest parent)
        for ($i = count($codeParts) - 1; $i > 0; $i--) {
            $parentCode = implode('.', array_slice($codeParts, 0, $i));
            if (isset($codeMapping[$parentCode])) {
                return $codeMapping[$parentCode];
            }
        }

        // If no parent found, use OPD as parent
        return $codeMapping[$opdCode] ?? null;
    }

}

