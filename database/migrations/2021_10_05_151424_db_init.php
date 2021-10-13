<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DbInit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pasar', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('lokasi')->nullable();
            $table->integer('kelas')->nullable();
            $table->timestamps();
        });

        Schema::create('kios', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nomor')->nullable();
            $table->unsignedBigInteger('id_pasar')->index();
            $table->enum('kategori', ['kios', 'los', 'pelataran']);
            $table->string('ukuran');
            $table->timestamps();

            $table->foreign('id_pasar')->references('id')->on('pasar')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('pedagang', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nik')->nullable();
            $table->string('nama')->nullable();
            $table->string('alamat')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->timestamps();
        });

        Schema::create('jenis_dagangan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nama')->nullable();
            $table->string('kategori')->nullable();
            $table->timestamps();
        });

        Schema::create('situ', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode')->nullable();
            $table->unsignedBigInteger('id_pasar')->index();
            $table->unsignedBigInteger('id_kios')->index();
            $table->unsignedBigInteger('id_pedagang')->index();
            $table->unsignedBigInteger('id_jenis_dagangan')->index();
            $table->date('tanggal_terbit');
            $table->date('berlaku_sampai');
            $table->timestamps();
            $table->softDeletesTz('deleted_at', 0);

            $table->foreign('id_pasar')->references('id')->on('pasar')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_kios')->references('id')->on('kios')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_pedagang')->references('id')->on('pedagang')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('id_jenis_dagangan')->references('id')->on('jenis_dagangan')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('situ_registration', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_situ')->index();
            $table->integer('registration');
            $table->boolean('is_registered')->default(false);
            $table->timestamps();

            $table->foreign('id_situ')->references('id')->on('situ')
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
        Schema::dropIfExists('pasar');
        Schema::dropIfExists('kios');
        Schema::dropIfExists('pedagang');
        Schema::dropIfExists('jenis_dagangan');
        Schema::dropIfExists('jenis_dagangan');
        Schema::dropIfExists('situ');
        Schema::dropIfExists('situ_registration');
    }
}
