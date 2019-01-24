<?php

namespace App\Http\Middleware;

use App\Models\BuildingList;
use App\Models\Resource;
use App\Policy;
use App\Service\BuildingService;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AutoCheck
{
    protected $buildingService = null;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!Auth::check()) {
            $response = $next($request);
            if (Auth::check()) $this->check();
        } else {
            $this->check();
            $response = $next($request);
        }

        return $response;
    }

    protected function check()
    {
        $this->buildingCheck();
        $this->policyCheck();
    }

    /* 检查器总成 */
    /**
     * 检查建筑列表的队列情况
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    protected function buildingCheck()
    {
        $this->buildingService = new BuildingService();
        $this->buildingService->init();

        foreach ($this->buildingService->schedules as $key => $item) {
            if ($item->endTime <= nowTime()) {
                switch ($item->action) {
                    case BuildingList::ACTION_BUILD:
                        $result = $this->buildingService->build($key);
                        if ($result[0] !== 'succeed') return response($result[1], 500);
                        break;
                    case BuildingList::ACTION_DESTROY:
                        $result = $this->buildingService->destroy($key);
                        if ($result[0] !== 'succeed') return response($result[1], 500);
                        break;
                }
            }
        }
    }

    /**
     * 检查政策的执行情况
     */
    protected function policyCheck()
    {
        // 获取用户执行完毕的政策
        $policyModels = Policy::where([
            ['status', '=', Policy::STATUS['doing']],
            ['userId', '=', Auth::id()],
        ])->get();

        // 执行对应政策
        foreach ($policyModels as $model) {
            $action = Policy::POLICIES_TRANS[$model->policiesKey]['action'];
            $this->$action($model->x, $model->y);
        }
    }


    /**
     * 流民招募启示的进度
     *
     * @param int $x
     * @param int $y
     * @return array|bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|string
     */
    protected function knowEnlisting(int $x, int $y)
    {
        // 启动政策
        $percent = rand(1, 100);
        // 10% 1-2; 60% 3-6; 30% 7-10; sum: 21.3
        if ($percent < 10) {
            $endResult = rand(1, 2);
        } elseif ($percent < 70) {
            $endResult = rand(3, 6);
        } else {
            $endResult = rand(7, 10);
        }
        $endInfo = '政策“' . Policy::POLICIES_TRANS[1]['name'] . '”已结束，有' . $endResult . '位流浪人投靠我们。';

        DB::beginTransaction();
        try {
            $result = (new Policy())->getStatus(Policy::POLICIES_ENLISTING['id'], Auth::id(), $x, $y, $endInfo);

            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }

            // 增加资源
            if (!is_array($result) && $result === $endInfo) {
                $resource = Resource::where('userId', Auth::id())->first();
                $resource->people += $endResult;
                if (!$resource->save()) {
                    throw new \Exception('保存新增流浪人失败');
                }
            }

            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }

    /**
     * 居民驱逐通告的进度
     *
     * @param int $x
     * @param int $y
     * @return array|bool|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response|string
     */
    protected function knowDeported(int $x, int $y)
    {
        // 检查
        $policy = Policy::where([
            ['x', '=', $x],
            ['y', '=', $y],
            ['policiesKey', '=', Policy::POLICIES_DEPORTED['id']],
            ['status', '=', Policy::STATUS['doing']],
        ])->first();
        if (!$policy) {
            return '这片土地尚未施行该政策，或该政策已失效';
        }

        $endInfo = '政策“' . Policy::POLICIES_TRANS[2]['name'] . '”结束了，我们狠心地驱逐了' . $policy->tips . '位居民。';
        DB::beginTransaction();
        try {
            $result = (new Policy())->getStatus(Policy::POLICIES_DEPORTED['id'], Auth::id(), $x, $y, $endInfo);

            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }

            // 增加资源
            $resource = Resource::where('userId', Auth::id())->first();
            $resource->people -= $policy->tips;
            if (!$resource->save()) {
                throw new \Exception('保存驱逐居民失败');
            }

            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }
}
