<?php

/**
 * 初始化环境
 */
function initDevelopment()
{
    exec('php artisan migrate:refresh --seed');
    exec('echo "" > storage/logs/laravel.log');
}

/**
 * 设置建筑清单（瞬时）
 */
function setBuildingListOnInstant() {
    $list = [
        'farm' => [
            [
                'name' => '一级农田',
                'level' => 1,
                'time' => 1,
                'product' => [
                    'food' => 40,
                ],
                'material' => [
                    'wood' => 10,
                ],
                'occupy' => [
                    'people' => 1,
                ],
            ]
        ],
    ];
    $buildingList = json_encode($list);
    if (!\Illuminate\Support\Facades\Redis::set('buildingList', $buildingList));
}
