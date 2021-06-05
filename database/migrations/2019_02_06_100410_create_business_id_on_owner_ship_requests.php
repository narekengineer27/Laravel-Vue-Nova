<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessIdOnOwnerShipRequests extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ownership_requests', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->uuid('business_id')->after('id');
            } else {
                $table->uuid('business_id')->nullable()->after('id');
            }
        });
    }
}
