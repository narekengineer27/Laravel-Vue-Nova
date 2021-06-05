<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOwnershipRequestUuidColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ownership_requests', function (Blueprint $table) {
            if (DB::getDriverName() === 'sqlite') {
                $table->uuid('uuid')->unique()->nullable()->after('id');
            } else {
                $table->uuid('uuid')->unique()->after('id');
            }
            $table->unique('user_id'); // A user can only submit one ownership request per business.
        });
    }
}
