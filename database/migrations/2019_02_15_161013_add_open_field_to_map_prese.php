<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenFieldToMapPrese extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('map_presets', function (Blueprint $table) {
            $table->boolean('only_open_businesses')->after('subtitle')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('map_presets', function (Blueprint $table) {
            $table->dropColumn('only_open_businesses');
        });
    }
}
