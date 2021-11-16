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
        Schema::create('skpd', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('partners', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('address');
            $table->string('director');
            $table->timestamps();
        });

        Schema::create('projects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_skpd')->index();
            $table->enum('type', ['perencanaan' . 'kontruksi', 'pengawasan']);
            $table->string('name');
            $table->date('spk_date');
            $table->string('spk_number');
            $table->double('spk_amount');
            $table->unsignedBigInteger('id_partner')->index();
            $table->double('spk_days');
            $table->date('spk_start');
            $table->date('spk_end');
            $table->double('adendum_days');
            $table->date('adendum_start');
            $table->date('adendum_end');
            $table->enum('payment_type', ['paid', 'installment']);
            $table->double('amount');
            $table->unsignedBigInteger('id_kpa')->index();
            $table->string('lokasi');
            $table->timestamps();

            $table->foreign('id_skpd')->references('id')->on('skpd')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_partner')->references('id')->on('partners')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('id_kpa')->references('id')->on('users')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('project_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_project')->index();
            $table->string('type');
            $table->double('amount');
            $table->boolean('paid')->default(false);
            $table->timestamps();

            $table->foreign('id_project')->references('id')->on('projects')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('payment_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_payment')->index();
            $table->string('type');
            $table->text('content');
            $table->timestamps();

            $table->foreign('id_payment')->references('id')->on('project_payments')
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
        Schema::dropIfExists('skpd');
        Schema::dropIfExists('partners');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_payments');
        Schema::dropIfExists('payment_detail');
    }
}
