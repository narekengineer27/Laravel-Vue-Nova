<?php

use Faker\Generator as Faker;

$factory->define(App\Bookmark::class, function (Faker $faker) {
    return [
        'business_id' => $factory->create(App\Models\Business::class)->id,
        'user_id' => $factory->create(App\Models\User::class)->id,
    ];
});
