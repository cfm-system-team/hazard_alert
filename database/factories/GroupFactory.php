<?php

/** @var Factory $factory */

use App\Group;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

$factory->define(Group::class, function (Faker $faker) {
    // hashを生成。重複した場合は生成し直す。
    do {
        $hash = hash('sha256', Str::random(60));
    } while (Group::withTrashed()->where('hash', $hash)->exists());

    return [
        'name' => $faker->company,
        'owner' => $faker->name,
        'telephone' => $faker->randomNumber(9, true),
        'email' => $faker->email,
        'zip_code' => $faker->randomNumber(7, true),
        'address' => $faker->address,
        'start_at' => now(),
        'end_at' => now()->addDays(5),
        'hash' => $hash,
        'agreed_at' => now()
    ];
});
