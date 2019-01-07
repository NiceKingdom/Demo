<?php

namespace App\Http\Middleware;

use App\Models\Building;
use App\Models\Resource;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class ResourceAuto
{
    // 生育率（现实秒）
    const PEOPLE_RATE_FERTILITY = 1.0014;
    // 饥荒死亡率（现实秒）
    const PEOPLE_RATE_STARVE = 0.9995;

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
     * @return bool
     * @throws \Exception
     */
    protected function resourceUpdate()
    {
        $resource = Resource::where('userId', Auth::id())->first();

        $time = $_SERVER['REQUEST_TIME'];

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
            $workRate = $resource->people / $workerNeed;
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

        // 人头税
        $interim = exploreTwo($time * $resource->people * 20 + $resource->moneyChip);
        $resource->money += $interim[0];
        $resource->moneyChip = $interim[1];

        // 粮食
        $interim = exploreTwo($time * $resource->foodOutput * $workRate + $resource->foodChip);
        $resource->food += $interim[0];
        $resource->foodChip = $interim[1];

        // 人口自增
        if ($workerNeed * 2 > $resource->people) {
            // 0.12% 为临时的每秒人口增长率
            $peopleAdd = $resource->people * pow(0.0006, $time);
            $interim = exploreTwo($peopleAdd + $resource->peopleChip);
            $resource->people += $interim[0];
            $resource->peopleChip = $interim[1];
        }

        // 计算粮食消耗
        $needFood = $resource->people * $time * 24;
        if ($needFood > $resource->food) {
            $interim = exploreTwo($needFood);
            if ($interim[1] > $resource->foodChip) {
                $interim[0] += 1;
                $interim[1] = 1 - $interim[1] + $resource->foodChip;
            }
            $resource->food -= $interim[0];
            $resource->foodChip -= ($interim[1] > $resource->foodChip) ? 0 : $interim[1];
        } else {
            $resource->people = floor($resource->foodOutput / 0.1 * 0.99);
        }

        $resource->save();
    }
}
