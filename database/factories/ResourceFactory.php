<?php

use Faker\Generator;
use Faker\Factory;

$factory->define(App\Models\Resource::class, function (Generator $faker) {
    $list = json_decode(\Illuminate\Support\Facades\Redis::get('buildingList'), true);

    return [
        'userId' => $_COOKIE['id'],
        'foodOutput' => $list['farm'][0]['product']['food'] * 20,
        'woodOutput' => $list['sawmill'][0]['product']['wood'] * 10,
    ];
});
