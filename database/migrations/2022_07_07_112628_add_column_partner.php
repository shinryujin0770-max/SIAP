<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnPartner extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('name_partner')->nullable()->after('id_partner');
            $table->text('address_partner')->nullable()->after('id_partner');
            $table->string('director_partner')->nullable()->after('id_partner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('name_partner');
            $table->dropColumn('address_partner');
            $table->dropColumn('director_partner');
        });
    }
}
