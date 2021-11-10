<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSignatureRegis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('situ_registration', function (Blueprint $table) {
            $table->unsignedBigInteger('id_signature')->index()->nullable()->after('is_registered');

            $table->foreign('id_signature')->references('id')->on('users')
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
        Schema::table('situ_registration', function (Blueprint $table) {
            $table->dropColumn('id_signature');
        });
    }
}
