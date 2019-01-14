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
 * @property string category 类型
 */
class UserHistory extends Model
{
    public const CATEGORY = [
        'policy' => 1,
    ];

    /**
     * 新增日志
     *
     * @param int $userId
     * @param int $x
     * @param int $y
     * @param string $endInfo
     * @param int $category
     * @return array|bool
     */
    public static function add(int $userId, int $x, int $y, string $endInfo, int $category)
    {
        $userHistory = new self();
        $userHistory->userId = $userId;
        $userHistory->x = $x;
        $userHistory->y = $y;
        $userHistory->info = $endInfo;
        $userHistory->category = $category;

        if (!$userHistory->save()) {
            return ['status' => 500, 'info' => '政策日志保存失败'];
        }

        return false;
    }
}
