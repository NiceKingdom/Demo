<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class UserHistory
 *
 * @package Policy
 * @property int userId 用户名
 * @property int x X 坐标
 * @property int y Y 坐标
 * @property int policiesKey 政策编号
 * @property string title 标题
 * @property string info 文字描述
 * @property int endTime 结束时间
 * @property int status 状态
 * @property string tips 备注（闲置字段）
 */
class Policy extends Model
{
    public const STATUS = [
        'stop' => 0,
        'doing' => 1,
        'end' => 2,
    ];
    public const POLICIES_TRANS = [
        1 => ['name' => '流民招募启示', 'action' => 'knowEnlisting'],
        2 => ['name' => '居民驱逐通告', 'action' => 'knowDeported'],
    ];
    public const POLICIES_ENLISTING = [
        'id' => 1,
        'time' => 25,
    ];
    public const POLICIES_DEPORTED = [
        'id' => 2,
        'time' => 100,
    ];

    public function getStatus(int $key, int $userId, int $x, int $y, string $endInfo)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $policy = self::where(['x' => $x, 'y' => $y, 'policiesKey' => $key, 'status' => self::STATUS['doing']])->first();
        if (!$policy) {
            return '这片土地尚未施行该政策，或该政策已失效';
        }
        // FIXME：Map 完成后，改为校验“土地所有者”
        if ($policy->userId !== $userId) {
            return ['status' => 403, 'info' => '这是块陌生的土壤，我们没有施政权力。'];
        } else if ($policy->endTime > $_SERVER['REQUEST_TIME']) {
            return $policy->endTime;
        } else {
            $policy->status = self::STATUS['end'];
            $result = UserHistory::add($userId, $x, $y, $endInfo, UserHistory::CATEGORY['policy']);
            if ($result) return $result;
            if (!$policy->save()) return ['status' => 500, 'info' => '保存政策失败'];

            return $endInfo;
        }
    }

    /**
     * 为用户自己启用政策
     *
     * @param int $x X 坐标
     * @param int $y Y 坐标
     * @param int $key 政策 ID
     * @param int $endTime 结束时间
     * @param string $tips 备注
     * @return bool|string
     */
    public function addWithMe(int $key, int $x, int $y, int $endTime = 0, string $tips = '')
    {
        return $this->add($key, Auth::id(), $x, $y, $endTime, $tips);
    }

    /**
     * 为用户自己废止政策
     *
     * @param int $x X 坐标
     * @param int $y Y 坐标
     * @param int $key 政策 Key
     * @return array|bool
     */
    public function stopWithMe(int $key, int $x, int $y)
    {
        $policy = self::where(['x' => $x, 'y' => $y, 'policiesKey' => $key, 'status' => self::STATUS['doing']])->first();
        if (!$policy || ($policy && $policy->endTime < $_SERVER['REQUEST_TIME'])) {
            return '这片土地尚未施行该政策，或该政策已失效';
        }

        if (Auth::id() === $policy->userId) {
            if ($policy->delete()) {
                $result = UserHistory::add(
                    $x, $y, Auth::id(),
                    '政策“' . self::POLICIES_TRANS[$policy->policiesKey]['name'] . '”被废止。',
                    UserHistory::CATEGORY['policy']
                );
                if ($result) return $result;

                DB::commit();
                return '政策已终止';
            }
        }

        return ['status' => 500, 'info' => '终止政策失败'];
    }

    /**
     * 启用政策
     *
     * @param int $key 政策 ID
     * @param int $userId 用户 ID
     * @param int $x X 坐标
     * @param int $y Y 坐标
     * @param int $endTime 结束时间
     * @param string $tips 备注
     * @return bool|array
     */
    protected function add(int $key, int $userId, int $x, int $y, int $endTime = 0, string $tips = '')
    {
        // 启用政策
        $model = new self();
        $model->x = $x;
        $model->y = $y;
        $model->status = self::STATUS['doing'];
        $model->userId = $userId;
        $model->policiesKey = $key;
        $model->title = self::POLICIES_TRANS[$key]['name'];
        if ($endTime) {
            $model->endTime = $endTime;
        }
        if ($tips) {
            $model->tips = $tips;
        }

        if ($model->save()) {
            $result = UserHistory::add(
                $x, $y, $userId,
                '政策“' . self::POLICIES_TRANS[$model->policiesKey]['name'] . '”启动。',
                UserHistory::CATEGORY['policy']
            );

            if ($result) return $result;
            return $endTime;
        }

        return ['status' => 500, 'info' => '政策启动失败'];
    }
}
