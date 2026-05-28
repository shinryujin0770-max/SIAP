<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRfkV2Tables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create rfk_v2 table (clone from rfk)
        Schema::create('rfk_v2', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_opd')->index();
            $table->string('month', 2)->index();
            $table->string('year', 4)->index();
            $table->string('status')->default('draft'); // draft, submit, approve
            $table->timestamps();

            // Unique constraint for one RFK per OPD per month per year
            $table->unique(['id_opd', 'month', 'year'], 'rfk_v2_unique');

            // Foreign key to opd table
            $table->foreign('id_opd')->references('id')->on('opd')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Create rfk_v2_detail table (clone from rfk_detail with hierarchy)
        Schema::create('rfk_v2_detail', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_rfk')->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->enum('type', ['opd', 'program', 'kegiatan', 'sub_kegiatan'])->default('sub_kegiatan')->index();
            $table->string('code')->index();
            $table->text('uraian')->nullable();
            $table->string('year', 4)->nullable()->index();

            // Financial columns
            $table->decimal('pagu_anggaran', 20, 2)->default(0);
            $table->decimal('bulan_lalu', 20, 2)->default(0);
            $table->decimal('bulan_ini', 20, 2)->default(0);
            $table->decimal('sd_bulan_ini', 20, 2)->default(0);
            $table->decimal('sisa_anggaran', 20, 2)->default(0);
            $table->decimal('angkas', 20, 2)->default(0);
            $table->decimal('angkas_prosentase', 10, 2)->default(0);
            $table->decimal('keuangan', 10, 2)->default(0);
            $table->decimal('fisik', 10, 2)->default(0);
            $table->decimal('fisik_rupiah', 20, 2)->default(0); // Shadow value for aggregation
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('id_rfk')->references('id')->on('rfk_v2')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('rfk_v2_detail')
                ->onUpdate('cascade')->onDelete('cascade');

            // Composite indexes for performance
            $table->index(['id_rfk', 'parent_id', 'type']);
            $table->index(['id_rfk', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rfk_v2_detail');
        Schema::dropIfExists('rfk_v2');
    }
}

