<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessReviewPhotoColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('business_reviews', function (Blueprint $table) {
            $table->string('review_photo')->nullable()->after('comment');
            $table->tinyInteger('mode')->nullable()->after('review_photo');
        });
    }
}
