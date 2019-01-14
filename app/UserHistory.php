<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UserHistory
 *
 * @package App
 * @property int userId 用户名
 * @property int x X 坐标
 * @property int y Y 坐标
 * @property string info 文字描述
 */
class UserHistory extends Model
{
    public static function add(int $userId, int $x, int $y, string $endInfo)
    {
        $userHistory = new self();
        $userHistory->userId = $userId;
        $userHistory->x = $x;
        $userHistory->y = $y;
        $userHistory->info = $endInfo;

        if (!$userHistory->save()) {
            return ['status' => 500, 'info' => '政策日志保存失败'];
        }

        return false;
    }
}
