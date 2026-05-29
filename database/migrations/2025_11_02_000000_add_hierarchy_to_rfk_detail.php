<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHierarchyToRfkDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfk_detail', function (Blueprint $table) {
            // Add hierarchy columns
            $table->unsignedBigInteger('parent_id')->nullable()->after('id_rfk')->index();
            $table->enum('type', ['opd', 'program', 'kegiatan', 'sub_kegiatan'])->default('sub_kegiatan')->after('parent_id')->index();
            $table->text('uraian')->nullable()->after('code');
            $table->string('year')->nullable()->after('id_rfk')->index();

            // Add foreign key for parent_id with cascade delete
            $table->foreign('parent_id')->references('id')->on('rfk_detail')
                ->onUpdate('cascade')->onDelete('cascade');

            // Add composite index for performance
            $table->index(['id_rfk', 'parent_id', 'type']);
        });

        // Set default values for existing records to 0 if they are null
        DB::statement('UPDATE rfk_detail SET pagu_anggaran = 0 WHERE pagu_anggaran IS NULL');
        DB::statement('UPDATE rfk_detail SET bulan_lalu = 0 WHERE bulan_lalu IS NULL');
        DB::statement('UPDATE rfk_detail SET bulan_ini = 0 WHERE bulan_ini IS NULL');
        DB::statement('UPDATE rfk_detail SET sd_bulan_ini = 0 WHERE sd_bulan_ini IS NULL');
        DB::statement('UPDATE rfk_detail SET sisa_anggaran = 0 WHERE sisa_anggaran IS NULL');
        DB::statement('UPDATE rfk_detail SET angkas = 0 WHERE angkas IS NULL');
        DB::statement('UPDATE rfk_detail SET angkas_prosentase = 0 WHERE angkas_prosentase IS NULL');
        DB::statement('UPDATE rfk_detail SET keuangan = 0 WHERE keuangan IS NULL');
        DB::statement('UPDATE rfk_detail SET fisik = 0 WHERE fisik IS NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfk_detail', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['parent_id']);

            // Drop indexes
            $table->dropIndex(['parent_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['year']);
            $table->dropIndex(['id_rfk', 'parent_id', 'type']);

            // Drop columns
            $table->dropColumn(['parent_id', 'type', 'uraian', 'year']);
        });
    }
}

