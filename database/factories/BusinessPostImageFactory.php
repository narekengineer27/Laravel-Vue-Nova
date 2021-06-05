<?php

use Faker\Generator as Faker;

$factory->define(App\Models\BusinessPostImage::class, function (Faker $faker) use ($factory) {
    $uuid = $faker->uuid;
    $date = \Carbon\Carbon::now();
    $stem = $date->format('Y-m-d_H') . '/';
    $company = strtolower($faker->company);
    $company = preg_replace('/[^a-zA-Z0-9-_\.]/','', $company);
    $stem .= $company . '/' . $faker->sha256 . '.jpg';

    return [
        'uuid'              => $uuid,
        'business_post_id'  => $factory->create(App\Models\BusinessPost::class)->id,
        'path'              => $stem
    ];
});
