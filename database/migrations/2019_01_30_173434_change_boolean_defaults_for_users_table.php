<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeBooleanDefaultsForUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_location_tracking')->default(1)->change();
            $table->boolean('post_publicly')->default(1)->change();
            $table->boolean('profile_visible')->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('allow_location_tracking')->default(0)->change();
            $table->boolean('post_publicly')->default(0)->change();
            $table->boolean('profile_visible')->default(0)->change();
        });
    }
}
