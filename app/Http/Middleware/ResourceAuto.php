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
     *
     * @return bool
     * @throws \Exception
     */
    protected function resourceUpdate()
    {
        $resource = Resource::where('userId', Auth::id())->first();

        $time = $_SERVER['REQUEST_TIME'];

        // 定义系统数据
        $time -= strtotime($resource->updated_at);
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
        $interim = exploreTwo($time * $resource->woodOutput * $workRate);
        $resource->wood = $interim[0];
        $resource->woodChip = $interim[1];

        // 石头
        $interim = exploreTwo($time * $resource->stoneOutput * $workRate);
        $resource->stone = $interim[0];
        $resource->stoneChip = $interim[1];

        // 人头税
        $interim = exploreTwo($time * $resource->people * 0.05);
        $resource->money = $interim[0];
        $resource->moneyChip = $interim[1];

        // 人口自增
        if ($workerNeed > $resource->people) {
            $peopleAdd = $time * 0.012;
            $interim = exploreTwo($peopleAdd);
            $resource->people = $interim[0];
            $resource->peopleChip = $interim[1];
        }

        $resource->save();
    }
}
