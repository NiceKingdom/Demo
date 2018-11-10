<?php

use Faker\Generator;
use Faker\Factory;

$factory->define(App\Models\BuildingList::class, function (Generator $faker) {
    return [
        'userId' => $_COOKIE['id'],
        'startTime' => 0,
        'endTime' => 0,
        'type' => '',
        'level' => 0,
        'action' => 0,
        'number' => 0,
    ];
});
