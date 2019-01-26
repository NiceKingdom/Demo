<?php

namespace App\Http\Middleware;

use App\Models\Building;
use App\Models\Resource;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ResourceAuto
{
    // 周期计算：foodMin: 0.67; foodMax: 30.13; foodMaxNum: 192.00
    // 公式：
    // 间隔时间 % 周期单位长度
    // 生育率（现实秒）
    const PEOPLE_RATE_FERTILITY = 1.0004;
    // 饥荒死亡率（现实秒）
    const PEOPLE_RATE_STARVE = 0.9995;
    // 食用粮食（现实秒）
    const PEOPLE_NEED_FOOD = 24;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            $response = $next($request);
            if (Auth::check()) $this->resourceUpdate();
        } else {
            $this->resourceUpdate();
            $response = $next($request);
        }

        return $response;
    }

    /**
     * 资源计算并自增
     * 当前的数值 = 更新前的数值 * 增长率 ^ 时间间隔
     *
     * @return bool|void
     * @throws \Exception
     */
    protected function resourceUpdate()
    {
        $resource = Resource::where('userId', Auth::id())->first();

        $time = nowTime();

        // 定义系统数据
        $time = ($time - strtotime($resource->updated_at)) / 3600;
        // 计算所有建筑需求工人
        $buildList = json_decode(Redis::get('buildingList'), true);
        $building = Building::where('userId', Auth::id())->first();
        $workerNeed = 0;
        foreach ($buildList as $key => $items) {
            foreach ($items as $level => $item) {
                if (array_key_exists('people', $item['occupy'])) {
                    $level++;
                    if ($level < 10) {
                        $level = '0' . $level;
                    }
                    $name = $key . $level;
                    $workerNeed += $item['occupy']['people'] * $building->$name;
                }
            }
        }

        // 计算平均效率
        if ($workerNeed) {
            if ($resource->people >= $workerNeed) {
                $workRate = 1;
            } else {
                $workRate = $resource->people / $workerNeed;
            }
        } else {
            $workRate = 0;
        }

        // 计算木材产量
        $interim = exploreTwo($time * $resource->woodOutput * $workRate + $resource->woodChip);
        $resource->wood += $interim[0];
        $resource->woodChip = $interim[1];

        // 石头
        $interim = exploreTwo($time * $resource->stoneOutput * $workRate + $resource->stoneChip);
        $resource->stone += $interim[0];
        $resource->stoneChip = $interim[1];

        /* 人口与粮食的生产模型 */
        // 粮食
        $interim = exploreTwo($time * $resource->foodOutput * $workRate + $resource->foodChip);
        $resource->food += $interim[0];
        $resource->foodChip = $interim[1];

        // 人口自增
        $peopleAdd = $resource->people * pow(self::PEOPLE_RATE_FERTILITY, $time) + $resource->peopleChip;
        $interim = exploreTwo($peopleAdd);
        $resource->people = $interim[0];
        $resource->peopleChip = $interim[1];

        // 现存人口消耗的粮食 > 粮食产量
        if (self::PEOPLE_NEED_FOOD * $resource->people >= $resource->foodOutput) {
            // 计算完美平衡
            $resource->people = floor($resource->foodOutput / self::PEOPLE_NEED_FOOD);
            $resource->food = 0;
            $resource->foodChip = 0.01;
        } else {
            // 消耗的粮食
            $needFood = $resource->people * $time * self::PEOPLE_NEED_FOOD;
            $interim = exploreTwo($needFood);
            if ($interim[1] > $resource->foodChip) {
                $interim[0] += 1;
                $resource->foodChip = 1 - $interim[1] + $resource->foodChip;
            } else {
                $resource->foodChip -= $interim[1];
            }
            $resource->food -= $interim[0];
        }

        if ($resource->food < 0 ) {
            $resource->food = 0;
            $resource->foodChip = 0.01;
        }

        if ($resource->people < 0 ) {
            $resource->people = 0;
        }

        // 人头税，每人每小时缴纳 12.6
        $interim = exploreTwo($time * $resource->people / 2 * 12.6 + $resource->moneyChip);
        $resource->money += $interim[0];
        $resource->moneyChip = $interim[1];

        $resource->save();
    }
}
