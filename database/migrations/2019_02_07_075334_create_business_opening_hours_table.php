<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessOpeningHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('business_opening_hours', function (Blueprint $table) {
                $table->uuid('id');
                $table->uuid('business_id');
                $table->tinyInteger('day')->nullable()->comment('
                 1 - Monday, 2 - Tuesday etc.
                ');
                $table->time('startTime')->nullable();
                $table->time('endTime')->nullable();
                $table->date('exceptionDate')->nullable();
                $table->string('exceptionName')->nullable();
                $table->boolean('isAnnualReoccuringException')->default(0)->nullable();
                $table->boolean('isException')->default(0)->nullable();
                $table->softDeletes();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('business_opening_hours');
    }
}
