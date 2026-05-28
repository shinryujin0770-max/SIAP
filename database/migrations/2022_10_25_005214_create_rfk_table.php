<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfkTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rfk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_opd')->index();
            $table->string('month');
            $table->enum('status', ['draft', 'submit', 'approve']);
            $table->timestamps();

            $table->foreign('id_opd')->references('id')->on('opd')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('rfk_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_rfk')->index();
            $table->string('code');
            $table->double('pagu_anggaran');
            $table->double('bulan_lalu');
            $table->double('bulan_ini');
            $table->double('sd_bulan_ini');
            $table->double('sisa_anggaran');
            $table->double('angkas');
            $table->double('angkas_prosentase');
            $table->double('keuangan');
            $table->double('fisik');
            $table->text('keterangan');
            $table->timestamps();

            $table->foreign('id_rfk')->references('id')->on('rfk')
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
        Schema::dropIfExists('rfk');
    }
}
