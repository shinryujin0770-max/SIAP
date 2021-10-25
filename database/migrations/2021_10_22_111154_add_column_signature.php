<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSignature extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('situ', function (Blueprint $table) {
            $table->unsignedBigInteger('id_signature')->index()->nullable();
            $table->boolean('is_signatured')->default(false);

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
        Schema::table('situ', function (Blueprint $table) {
            $table->dropColumn('id_signature');
            $table->dropColumn('is_signatured');
        });
    }
}
