<?php

use Faker\Generator;
use Faker\Factory;

/*
capital:
    n*n player : n*3*n*3 block

    1*1:3*3
    2*2:6*6
    3*3:9*9
    4*4:12*12
    5*5:15*15
*/

$factory->define(App\User::class, function (Generator $faker) {
    static $password;

    $userPlant = 10000;
    $_COOKIE['id']++;

    return [
        'nickname' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
        'kingdom' => $faker->country . ((rand(1,2) === 1) ? '王国' : '王朝'),
        'capital' => ceil($_COOKIE['id'] / sqrt($userPlant)) * 3 - 1 . ',' . ((($_COOKIE['id']-1) % sqrt($userPlant) + 1) * 3 - 1),
    ];
});
