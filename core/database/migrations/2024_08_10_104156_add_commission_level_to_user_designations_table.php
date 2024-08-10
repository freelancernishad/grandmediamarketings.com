<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCommissionLevelToUserDesignationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_designations', function (Blueprint $table) {
            $table->unsignedInteger('commission_level')->default(1); // Add commission level column with a default value
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_designations', function (Blueprint $table) {
            $table->dropColumn('commission_level');
        });
    }
}
