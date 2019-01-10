<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Policy extends Model
{
    public const POLICIES_TRANS = [
        1 => '流民招募启示',
        2 => '居民驱逐通告',
    ];
    public const POLICIES_ENLISTING = [
        'id' => 1,
        'time' => 25,
    ];
    public const POLICIES_DEPORTED = [
        'id' => 2,
        'time' => 100,
    ];

    public function getStatus(int $x, int $y, int $userId = 0, string $endInfo)
    {
        if (!$userId) {
            $userId = Auth::id();
        }

        $policy = self::where(['x' => $x, 'y' => $y])->first();
        if (!$policy) {
            return ['status' => 200, 'info' => '这片土地尚未施行该政策'];
        }
        // FIXME：Map 完成后，改为校验“土地所有者”
        if ($policy->userId !== $userId) {
            return ['status' => 403, 'info' => '这是块陌生的土壤，我们没有施政权力。'];
        } else if ($policy->endTime > $_SERVER['REQUEST_TIME']) {
            return ['status' => 200, 'info' => $policy->endTime];
        } else {
            if (UserHistory::)
            // 行政
            $userHistory = new UserHistory();
            $userHistory->userId = $userId;
            $userHistory->status = UserHistory::STATUS['end'];
            $userHistory->info = $endInfo;

            if (!$userHistory->save()) {
                return ['status' => 500, 'info' => '政策日志保存失败'];
            }
            return ['status' => 200, 'info' => $endInfo];
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
    public function addWithMe(int $x, int $y, int $key, int $endTime = 0, string $tips = '')
    {
        return $this->add($key, Auth::id(), $x, $y, $endTime, $tips);
    }

    /**
     * 为用户自己废止政策
     *
     * @param int $x X 坐标
     * @param int $y Y 坐标
     * @return array|bool
     */
    public function stopWithMe(int $x, int $y)
    {
        $policy = self::where(['x' => $x, 'y' => $y])->first();
        if (!$policy || ($policy && $policy->endTime < $_SERVER['REQUEST_TIME'])) {
            return ['status' => 200, 'info' => '这片土地尚未施行该政策，或该政策已失效'];
        }

        if (Auth::id() === $policy->userId) {
            if ($policy->delete()) {
                $userHistory = new UserHistory();
                $userHistory->userId = Auth::id();
                $userHistory->status = UserHistory::STATUS['stop'];
                $userHistory->info = '政策“' . self::POLICIES_TRANS[$policy->policiesKey] . '”被废止。';

                if ($userHistory->save()) {
                    DB::commit();
                    return true;
                }
                return ['status' => 500, 'info' => '政策日志保存失败'];
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
        $model = new self();
        $model->x = $x;
        $model->y = $y;
        $model->userId = $userId;
        $model->policiesKey = $key;
        $model->title = self::POLICIES_TRANS[$key];
        if ($endTime) {
            $model->endTime = $endTime;
        }
        if ($tips) {
            $model->tips = $endTime;
        }

        if ($model->save()) {
            $userHistory = new UserHistory();
            $userHistory->userId = Auth::id();
            $userHistory->status = UserHistory::STATUS['doing'];
            $userHistory->info = '政策“' . self::POLICIES_TRANS[$model->policiesKey] . '”启动。';

            if (!$userHistory->save()) {
                return ['status' => 500, 'info' => '政策日志保存失败'];
            }
            DB::commit();
            return ['status' => 200, 'info' => $endTime];
        }

        return ['status' => 500, 'info' => '政策启动失败'];
    }
}
