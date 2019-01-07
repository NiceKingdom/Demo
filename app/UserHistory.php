<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserHistory extends Model
{
    public const STATUS = [
        'stop' => 0,
        'end' => 1,
        'doing' => 2,
    ];
}
