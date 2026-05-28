<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFisikRupiahToRfkDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rfk_detail', function (Blueprint $table) {
            // Add fisik_rupiah column for calculated physical realization in rupiah
            // Formula: fisik (%) * pagu_anggaran / 100
            $table->decimal('fisik_rupiah', 20, 2)->default(0)->after('fisik');
        });

        // Update existing records
        DB::statement('UPDATE rfk_detail SET fisik_rupiah = (fisik * pagu_anggaran / 100)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rfk_detail', function (Blueprint $table) {
            $table->dropColumn('fisik_rupiah');
        });
    }
}

