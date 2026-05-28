<?php

namespace App\Imports;

use App\Models\Rfk;
use App\Models\Rfk_detail;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class RfkImport implements ToArray, WithCalculatedFormulas
{
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(array $rows)
    {
        $date = $this->data['month'];
        $id_opd = $this->data['user']['id_opd'];
        foreach ($rows as $key => $row) {
            try {
                if ($key < 2 || $row[0] === null) continue;
                $data['code'] = $row[0];
                $data['uraian'] = $row[1];
                $data['pagu_anggaran'] = $row[2];
                $data['bulan_lalu'] = $row[3];
                $data['bulan_ini'] = $row[4];
                $data['sd_bulan_ini'] = $row[5];
                $data['sisa_anggaran'] = $data['pagu_anggaran'] - $data['sd_bulan_ini'];
                // $data['angkas'] = $row[7] ?? 0;
                // $data['angkas_prosentase'] = $data['pagu_anggaran'] ? number_format((float)$data['angkas'] / (float)$data['pagu_anggaran'] * 100, 2) : 0;
                $data['keuangan'] = $data['pagu_anggaran'] ? number_format((float) $data['sd_bulan_ini'] / (float)$data['pagu_anggaran'] * 100, 2) : 0;
                $data['fisik'] = number_format((float)$row[8], 2);
                $data['sisa'] = $data['pagu_anggaran'] ? (float) ($data['fisik'] * $data['pagu_anggaran'] / 100) : 0;
                $data['prosentase_fisik'] = $data['pagu_anggaran'] ? (float) ($data['sisa'] / $data['pagu_anggaran'] * 100) : 0;
                $data['keterangan'] = $row[9];

                $bulan = Carbon::parse($date)->format('m');
                $tahun = Carbon::parse($date)->format('Y');

                $rfk = Rfk::updateOrCreate(['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun], ['id_opd' => $id_opd, 'month' => $bulan, 'year' => $tahun]);

                $rfk_detail = Rfk_detail::updateOrCreate(['code' => $data['code'], 'id_rfk' => $rfk['id']], $data);
            } catch (\Exception $e) {
            }
        }
        return true;
    }
}
