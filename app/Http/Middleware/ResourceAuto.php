<?php

namespace App\Http\Middleware;

use App\Models\Building;
use App\Models\Resource;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Mockery\Exception;

class ResourceAuto
{
    // 生育率（现实秒）
    const PEOPLE_RATE_FERTILITY = 1.0014;
    // 饥荒死亡率（现实秒）
    const PEOPLE_RATE_STARVE = 0.9995;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
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
    | 属性 | 公式 | 备注 |
    |--|--|--|
    | 本秒工作效率 | Int(昨日终时人数 × 100%) | 同上 |
    | - | - | - |
    | 木材生产 | Int(理论产量 × (工作效率 ^ X)) | 单位：1s |
    | 石料生产 | Int(理论产量 × (工作效率 ^ X)) | 单位：1s |
    | 人口自增 | Int(终日人数 × (100.14% ^ X)) | 单位：1s |
    | 人口饥荒 | Int(终时人数 × (99.95% ^ X)) | 单位：1s |
    | 食物生产 | Int(理论产量 × (工作效率 ^ X)) | 单位：1s |
    | 食物消耗 | Int(雇佣人数 × (0.1 ^ X)) | 单位：1s |
    | 税收 | Int(终日人数 × (0.05 ^ X)) | 单位：1s |
     * @return bool
     * @throws \Exception
     */
    protected function resourceUpdate()
    {
//        $resource = Resource::where('userId', Auth::id())->first();
//
//        $time = $_SERVER['REQUEST_TIME'];
//        if ($resource->updated_at + 3 > $time) {
//            return false;
//        }
//
//        // 定义系统数据
//        $time -= $resource->updated_at;
//        $workPeople = $resource->people * pow(self::PEOPLE_RATE_FERTILITY - (1 - self::PEOPLE_RATE_STARVE), $time) / $time;
//        $workRate = $workPeople / $work;
//
//        // 木材 Int(理论产量 × ($workPeople ^ X))
//        $interim = $this->resourceUp([$resource->wood, $resource->woodChip], $manpower, $resource->woodOutput, $via);
//        $resource->wood = $interim['int'];
//        $resource->woodChip = $interim['chip'];
//
//        // 石头
//        $interim = $this->resourceUp([$resource->stone, $resource->stoneChip], $manpower, $resource->stoneOutput, $via);
//        $resource->stone = $interim['int'];
//        $resource->stoneChip = $interim['chip'];
//
//        // 钱财
//        $interim = $this->resourceUp([$resource->money, $resource->moneyChip], $manpower, $resource->moneyOutput, $via, 1, $deplete);
//        $resource->money = $interim['int'];
//        $resource->moneyChip = $interim['chip'];
//
//        $resource->save();
    }

    /**
     * 计算资源迭代后的实际内容
     * @param array $item 包含整数与碎片资源的数组
     * @param float $manpower (紧缺/非紧缺)劳动力系数
     * @param float $output 资源产出
     * @param float $via 时间长度
     * @param int $operate 操作，从 1 至 2 分别为加减。默认为 0，无运算
     * @param float $number 操作值，通过运算来计入相应的资源项
     * @return array
     */
    protected function resourceUp(array $item, float $manpower, float $output, float $via, int $operate = 0, float $number = 0)
    {
        $item['int'] = $item[0];
        $item['chip'] = $item[1];

        $interim = exploreTwo($output * $manpower * $via);
        $item['int'] += $interim[0] + intval($item['chip'] + $interim[1]);
        $item['chip'] += $interim[1];

        if (!$operate) {
            if ($item['chip'] >= 1) {
                $interim = exploreTwo($item['chip']);
                $item['int'] += $interim[0];
                $item['chip'] = $interim[1];
            }
        } else {
            // 启动运算
            $interim = exploreTwo($number);
            if ($operate === 1) {
                $item['int'] += $interim[0];
                $item['chip'] += $interim[1];

                if ($item['chip'] >= 1) {
                    $interim = exploreTwo($item['chip']);
                    $item['int'] += $interim[0];
                    $item['chip'] = $interim[1];
                }
            } elseif ($operate === 2) {
                $item['int'] -= $interim[0];
                $item['chip'] -= $interim[1];

                if ($item['chip'] >= 1 || $item['chip'] <= 0) {
                    $interim = exploreTwo($item['chip']);
                    $item['int'] += $interim[0];
                    $item['chip'] = $interim[1];

                    if ($item['chip'] < 0) {
                        $item['int'] -= 1;
                        $item['chip'] += 1;
                    }
                }
            }
        }
        unset($item[0]);
        unset($item[1]);

        return $item;
    }
}
