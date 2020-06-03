<?php

/** @var Factory $factory */

use App\Group;
use App\Recipient;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Recipient::class, function (Faker $faker) {
    return [
        'email' => $faker->email,
        'group_id' => Group::inRandomOrder()->first()->id,
        'agreed_at' => now()
    ];
});
