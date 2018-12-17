<?php
function setBuildingListOnInstant() {
    $list = [
        'farm' => [
            [
                'name' => '一级农田',
                'level' => 1,
                'time' => 75,
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
