<?php

use Faker\Generator as Faker;
use App\Models\Business;
use App\Models\User;

$factory->define(App\Models\BusinessReview::class, function (Faker $faker) {
    return [
        'uuid'        => $faker->uuid,
        // 'business_id' => factory(App\Models\Business::class),
        // 'user_id'     => factory(App\Models\User::class),
        'business_id' => Business::inRandomOrder()->first(),
        'user_id'     => User::inRandomOrder()->first(),
        'score'       => $faker->randomDigitNotNull,
        'comment'     => $faker->text(50),
        'meta'        => $faker->text(50),
    ];
});
