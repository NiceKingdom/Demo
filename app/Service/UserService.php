<?php

namespace App\Service;

use App\User;
use Illuminate\Support\Facades\Redis;

class UserService
{
    public function getUser($id)
    {
        $result = User::select('id', 'nickname', 'email', 'created_at')
            ->where('id', $id)
            ->first();

        return $result;
    }

    public function checkRedis()
    {
        $result = [];
        $list = [
//            'buildLimit',
            'buildList',
//            'armyLimit',
            'armyList',
        ];

        foreach ($list as $item) {
            $key = getUserKey($item);
            $result[] = Redis::get($key) ?? Redis::set($key, json_encode([]));
        }

        if (in_array(false, $result))
            return false;
        else
            return true;
    }
}
