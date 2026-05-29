<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProjectsChecklistAddSignator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('project_checklists', function (Blueprint $table) {
            $table->string('nama')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('nip')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('project_checklists', function (Blueprint $table) {
            $table->dropColumn('nama');
            $table->dropColumn('jabatan');
            $table->dropColumn('nip');
        });
    }
}
