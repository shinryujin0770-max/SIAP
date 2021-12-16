<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKecamatanDesaPrject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->double('pagu_anggaran')->nullable()->after('id_kpa');
            $table->double('hps')->nullable()->after('pagu_anggaran');
            $table->integer('id_kecamatan')->index()->after('hps');
            $table->integer('id_kelurahan')->index()->after('id_kecamatan');

            $table->foreign('id_kecamatan')->references('id')->on('kecamatan')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_kelurahan')->references('id')->on('kelurahan')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
