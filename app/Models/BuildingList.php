<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BuildingList extends Model
{
    # 操作类型
    const TRANS_ACTION = [-1 => '未开启', '空闲', '施工中', '拆除中'];
    const ACTION_UNOPENED = -1;
    const ACTION_SLEEP = 0;
    const ACTION_BUILD = 1;
    const ACTION_DESTROY = 2;

    protected $fillable = [
        'userId', 'startTime', 'endTime', 'type', 'level', 'action', 'number', 'startTime', 'endTime'
    ];
}
