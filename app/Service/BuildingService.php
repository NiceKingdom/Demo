<?php

namespace App\Service;

use App\Models\Building;
use App\Models\BuildingList;
use App\Models\Resource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class BuildingService
{
    // 建筑队数量
    const CONSTRUCTION_MAX = 3;
    // 拆除回收的资源比例
    const RETURN_RATE = 0.35;
    // 取消建造时，放弃的已投入资源比例
    const ABANDON_RATE = 0.30;

    public $schedules = [];

    /**
     * Replace __construct function with life cycle
     */
    public function init()
    {
        $this->schedules = BuildingList::where(['userId' => Auth::id()])->get();
    }

    protected function getBuildingList()
    {
        $buildingList = json_decode(Redis::get('buildingList'), true);
        if (!$buildingList) {
            logService::common('未获取到 Redis 缓存的建筑列表', 404, 'Service\BuildingService::getBuildingList', 'Error');
        }

        return $buildingList;
    }

    /**
     * 增加建筑任务
     *
     * @param int $id 建筑队ID
     * @param int $action 操作类型
     * @param string $type 类型
     * @param int $level 级别
     * @param int $number 数量
     * @param int $startTime 开始时间
     * @param int $endTime 结束时间
     * @return bool|string 错误返回信息或 false
     */
    protected function addSchedule(int $id, int $action, string $type, int $level, int $number, int $startTime, int $endTime)
    {
        $schedule = BuildingList::find($id);
        if (!$schedule) {
            return '不存在此建筑队';
        }
        $schedule->action = $action;
        $schedule->type = $type;
        $schedule->level = $level;
        $schedule->number = $number;
        $schedule->startTime = $startTime;
        $schedule->endTime = $endTime;
        if (!$schedule->save()) {
            return '建筑计划保存失败';
        }

        return false;
    }

    /**
     * 删除建筑任务
     *
     * @param BuildingList $schedule
     * @return bool|string 错误返回信息
     */
    protected function deleteSchedule(BuildingList $schedule)
    {
        if (!$schedule) {
            return '不存在此建筑队';
        }
        $schedule->action = BuildingList::ACTION_SLEEP;
        $schedule->startTime = 0;
        $schedule->endTime = 0;
        if (!$schedule->save()) {
            return '建筑计划保存失败';
        }

        return false;
    }

    /**
     * 获取无所事事的建筑队
     * @return bool|mixed
     */
    protected function getSleep()
    {
        foreach ($this->schedules as $key => $schedule) {
            if ($schedule->action === BuildingList::ACTION_SLEEP) {
                return $schedule;
            }
        }

        return false;
    }

    /**
     * 开始建筑，扣住资源、占用
     *
     * @param string $type 建筑类型
     * @param int $level 建筑级别
     * @param int $number 建筑数量
     * @return array|string
     */
    public function buildBefore(string $type, int $level, int $number)
    {
        if (!$schedule = $this->getSleep()) {
            return ['failed', '施工队都在忙碌'];
        }

        $startTime = nowTime();
        // 计算结束时间
        $item = $this->getBuildingList();
        $item = $item[$type][$level - 1];
        $endTime = $startTime + $item['time'] * $number;

        $resource = Resource::where('userId', Auth::id())->first();
        // 扣除资源
        foreach ($item['material'] as $key => $value) {
            $deplete = $value * $number;
            if ($resource->$key < $deplete)
                return ['failed', '资源不足（消耗类）'];

            $resource->$key -= $deplete;
        }

        // 扣除占用
        foreach ($item['occupy'] as $key => $value) {
            $deplete = $value * $number;
            if ($resource->$key < $deplete)
                return ['failed', $key . '资源不足（占用类）'];

            $resource->$key -= $deplete;
        }

        DB::beginTransaction();
        try {
            if ($resource->save()) {
                $result = $this->addSchedule($schedule->id, BuildingList::ACTION_BUILD,
                    $type, $level, $number, $startTime, $endTime);
                if ($result) {
                    DB::rollBack();
                    return ['failed', $result];
                }
                DB::commit();
                return ['succeed', '施工队已经开始工作'];
            } else {
                throw new \Exception('因为意外，建筑队未能启动工作');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'BbbBT' . logService::common('建筑启动失败 - ' . $exception->getMessage(), 500, 'Service\BuildingService::destroy', 'Error');
            return response('意外情况，编号：' . $logID, 500);
        }
    }

    /**
     * 取消建造，返还部分资源，解除占用
     *
     * @param int $scheduleKey 施工队的键
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|string|\Symfony\Component\HttpFoundation\Response
     */
    public function buildRecall(int $scheduleKey)
    {
        $schedule = $this->schedules[$scheduleKey];

        $item = $this->getBuildingList();
        $item = $item[$schedule['type']][$schedule['level'] - 1];
        $resource = Resource::where('userId', Auth::id())->first();
        // 返还部分资源
        foreach ($item['material'] as $key => $value) {
            $resource->$key += intval($value * $schedule['number'] * (1 - self::ABANDON_RATE));
        }

        // 解除占用
        foreach ($item['occupy'] as $key => $value) {
            $resource->$key += $value * $schedule['number'];
        }

        DB::beginTransaction();
        try {
            if (!$resource->save()) {
                throw new \Exception('因为意外，建筑队未能终止工作');
            }

            // 删除建筑进程
            $this->deleteSchedule($this->schedules[$scheduleKey]->id);
            DB::commit();
            return ['succeed', '已取消该施工项目'];
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'JeihC' . logService::common('取消失败：' . $exception->getMessage(), 500, 'Service\BuildingService::buildRecall', 'Error');
            return response('意外情况，编号：' . $logID, 500);
        }
    }

    /**
     * 建筑建成，增加建筑数量、生产资源量
     *
     * @param int $scheduleKey 施工队的键
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function build(int $scheduleKey)
    {
        $schedule = $this->schedules[$scheduleKey];
        $type = $schedule->type;
        $level = $schedule->level;
        $number = $schedule->number;
        $item = $this->getBuildingList();
        $item = $item[$type][$level - 1];

        $resource = Resource::where('userId', Auth::id())->first();
        $building = Building::where('userId', Auth::id())->first();

        // 增加建筑数量
        if ($level < 10)
            $level = '0' . $level;
        $buildingName = $type . $level;
        $building->$buildingName += $number;

        // 增加产出
        foreach ($item['product'] as $key => $value) {
            $itemName = $key . 'Output';
            $resource->$itemName += $value * $number;
        }

        DB::beginTransaction();
        try {
            if ($resource->save() && $building->save()) {
                if ($result = $this->deleteSchedule($schedule)) {
                    throw new \Exception($result);
                }
                DB::commit();
                return ['succeed', '建筑完成'];
            } else {
                throw new \Exception('因未预料的意外，建筑失败');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'Bbs' . logService::common('建筑失败 - ' . $exception->getMessage(), 500, 'Service\BuildingService::build', 'Error');
            return ['failed', '意外情况，编号：' . $logID];
        }
    }

    /**
     * 开始拆除，降低建筑数量与产出、解除占用
     *
     * @param string $type 建筑类型
     * @param int $level 建筑级别
     * @param int $number 建筑数量
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroyBefore(string $type, int $level, int $number)
    {
        if (!$schedule = $this->getSleep()) {
            return ['failed', '无可用施工队'];
        }
        $startTime = nowTime();
        $item = $this->getBuildingList();
        $item = $item[$type][$level - 1];
        $endTime = $startTime + $item['time'] * $number;

        $resource = Resource::where('userId', Auth::id())->first();
        $building = Building::where('userId', Auth::id())->first();

        // 降低建筑数量
        if ($level < 10) $level = '0' . $level;
        $buildingName = $type . $level;
        if ($building->$buildingName < $number) {
            return ['failed', '建筑数量不足，难道我们去拆空气吗？'];
        }
        $building->$buildingName -= $number;

        // 降低产出
        foreach ($item['product'] as $key => $value) {
            $itemName = $key . 'Output';
            $resource->$itemName -= $value * $number;
        }

        // 解除占用
        foreach ($item['occupy'] as $key => $value) {
            $resource->$key += $value * $number;
        }

        DB::beginTransaction();
        try {
            // 增加建筑队列
            $result = $this->addSchedule($schedule->id, BuildingList::ACTION_DESTROY,
                $type, $level, $number, $startTime, $endTime);
            if ($result) {
                DB::rollBack();
                return ['failed', $result];
            }
            if ($resource->save() && $building->save()) {
                DB::commit();
                return ['succeed', '拆除启动'];
            } else {
                throw new \Exception('因未预料的意外，建筑拆除无法启动');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'BdC2c' . logService::common('拆除失败 - ' . $exception->getMessage(), 500, 'Service\BuildingService::destroy', 'Error');
            return response('意外情况，编号：' . $logID, 500);
        }
    }

    /**
     * 拆除完毕，返还部分资源
     *
     * @param int $scheduleKey 施工队的键
     * @return array|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function destroy(int $scheduleKey)
    {
        $schedule = $this->schedules[$scheduleKey];
        $type = $schedule->type;
        $level = $schedule->level;
        $number = $schedule->number;
        $item = $this->getBuildingList();
        $item = $item[$type][$level - 1];

        $resource = Resource::where('userId', Auth::id())->first();

        // 返还资源
        foreach ($item['material'] as $key => $value) {
            $resource->$key += intval($value * $number * self::RETURN_RATE);
        }

        DB::beginTransaction();
        try {
            if ($resource->save()) {
                if ($result = $this->deleteSchedule($schedule)) {
                    throw new \Exception($result);
                }
                DB::commit();
                return ['succeed', '建筑拆除了'];
            } else {
                throw new \Exception('因未预料的意外，建筑拆除失败');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'Bbs' . logService::common('拆除失败 - ' . $exception->getMessage(), 500, 'Service\BuildingService::destroy', 'Error');
            return ['failed', '意外情况，编号：' . $logID];
        }
    }
}
