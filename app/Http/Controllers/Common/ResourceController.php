<?php

namespace App\Http\Controllers\Common;

use App\Models\Resource;
use App\Policy;
use App\UserHistory;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ResourceController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * showdoc
     * @catalog 前后端接口/资源
     * @title [获取]用户的资源
     * @description -
     * @method get
     * @url https://{url}/user/get-resource
     * @return {"id":26,"userId":26,"people":200,"peopleChip":0,"peopleOutput":0,"food":3000,"foodChip":0,"foodOutput":0,"wood":2000,"woodChip":0,"woodOutput":0,"stone":1000,"stoneChip":0,"stoneOutput":0,"money":1322770,"moneyChip":0,"moneyOutput":0,"area":200,"areaChip":0,"areaOutput":0,"created_at":"2018-12-22 12:45:57","updated_at":"2018-12-24 01:24:47"}
     * @return_param id int 资源ID
     * @return_param userId int 用户ID
     * @return_param people int 人口数量（整数）
     * @return_param peopleChip int 人口数量（小数）
     * @return_param peopleOutput int 人口增长（小时）
     * @return_param wood int 木材数量（整数）
     * @return_param woodChip int 木材数量（小数）
     * @return_param woodOutput int 木材增长（小时）
     * @return_param stone int 石料数量（整数）
     * @return_param stoneChip int 石料数量（小数）
     * @return_param stoneOutput int 石料增长（小时）
     * @return_param money int 货币数量（整数）
     * @return_param moneyChip int 货币数量（小数）
     * @return_param moneyOutput int 货币增长（小时）
     * @return_param area int 土地面积（整数）
     * @return_param areaChip int 土地面积（小数）
     * @return_param areaOutput int 土地面积增长（永远为0）
     * @return_param created_at int 创建时间
     * @return_param updated_at int 更新时间
     * @number 10
     */
    public function getMeResource()
    {
        $info = Resource::where('userId', Auth::id())->first();

        return $info;
    }

    /**
     * showdoc
     * @catalog 前后端接口/领地
     * @title [获取]政策历史
     * @description -
     * @method post
     * @url https://{url}/lord/policy/history
     * @param x 必选 int X坐标
     * @param y 必选 int Y坐标
     * @param page 可选 int 当前页数，默认为1
     * @param size 可选 int 单页信息量，默认为10
     * @return [{"id":2,"info":"\u653f\u7b56\u201c\u5c45\u6c11\u9a71\u9010\u901a\u544a\u201d\u7ed3\u675f\u4e86\uff0c\u6211\u4eec\u72e0\u5fc3\u5730\u9a71\u9010\u4e861\u4f4d\u5c45\u6c11\u3002","created_at":"2019-01-15 02:30:06"},{"id":3,"info":"\u653f\u7b56\u201c\u5c45\u6c11\u9a71\u9010\u901a\u544a\u201d\u542f\u52a8\u3002","created_at":"2019-01-15 02:30:06"},{"id":4,"info":"\u653f\u7b56\u201c\u5c45\u6c11\u9a71\u9010\u901a\u544a\u201d\u7ed3\u675f\u4e86\uff0c\u6211\u4eec\u72e0\u5fc3\u5730\u9a71\u9010\u4e861\u4f4d\u5c45\u6c11\u3002","created_at":"2019-01-15 02:30:10"}]
     * @return_param id int 事件ID
     * @return_param info int 简述
     * @return_param created_at int 发生时间
     * @number 10
     */
    public function getPolicyHistory(Request $request)
    {
        $page = intval($request['page'] ?? 1);
        $size = intval($request['size'] ?? 10);
        $x = (int)$request['x'];
        $y = (int)$request['y'];
        if ($page < 1 || $size < 1 || !$x || !$y) {
            return response('参数错误', 400);
        }
        $page -= 1;

        return UserHistory::select('id', 'info', 'created_at')
            ->where([
                ['x', '=', $x],
                ['y', '=', $y],
                ['userId', '=', Auth::id()],
                ['category', '=', UserHistory::CATEGORY['policy']],
            ])
            ->offset($page * $size)
            ->limit($size)
            ->get();
    }

    /**
     * showdoc
     * @catalog 前后端接口/领地
     * @title [动作]发布流民招募启示
     * @description 在坐标为 X,Y 的领土上，施展政令。该政令耗费 10 金钱，返回政令的结束时间戳，届时，需前端发起请求，获取该政令的状态并提示给用户
     * @method post
     * @url https://{url}/lord/policy/enlisting/open
     * @param x 必选 int X坐标
     * @param y 必选 int Y坐标
     * @return 1546777084
     * @return_param - int|string 政令完成的时间戳或失败原因
     * @number 50
     */
    public function openEnlisting(Request $request)
    {
        // 扣除资源
        $resource = Resource::where('userId', Auth::id())->first();
        if ($resource->money < 10) {
            return response('大爷，咱们的钱不够啦！', 400);
        }
        $resource->money -= 10;
        DB::beginTransaction();
        try {
            $coordinate = explode(',', Auth::user()->capital);
            $policy = Policy::where(['x' => $coordinate[0], 'y' => $coordinate[1], 'policiesKey' => Policy::POLICIES_ENLISTING['id'], 'status' => Policy::STATUS['doing']])->first();
            if ($policy) {
                $endInfo = '政策“' . Policy::POLICIES_TRANS[2]['name'] . '”结束了，我们狠心地驱逐了' . $policy->tips . '位居民。';
                $check = (new Policy())->getStatus(Policy::POLICIES_DEPORTED['id'], Auth::id(), $coordinate[0], $coordinate[1], $endInfo);
                if (is_numeric($check)) {
                    return '政策实施中，请勿重复施政';
                }
            }

            if (!$resource->save()) {
                return '失败：付钱行为无法保存';
            }

            // 启动政策
            $endTime = Policy::POLICIES_ENLISTING['time'] + $_SERVER['REQUEST_TIME'];
            $result = (new Policy())->addWithMe(Policy::POLICIES_ENLISTING['id'], $coordinate[0], $coordinate[1], $endTime);

            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }

    /**
     * showdoc
     * @catalog 前后端接口/领地
     * @title [动作]终止流民招募启示
     * @description 直接结束招募，但不退还发执行政令的费用
     * @method get
     * @url https://{url}/lord/policy/enlisting/stop/{x}/{y}
     * @param x 必选 int X坐标
     * @param y 必选 int Y坐标
     * @return 政策已终止
     * @return_param - string 提示语
     * @number 50
     */
    public function stopEnlisting(int $x, int $y)
    {
        // 启动政策
        $coordinate = explode(',', Auth::user()->capital);
        DB::beginTransaction();
        try {
            $result = (new Policy())->stopWithMe(Policy::POLICIES_ENLISTING['id'], $coordinate[0], $coordinate[1]);

            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }

    /**
     * showdoc
     * @catalog 前后端接口/领地
     * @title [动作]发布居民驱逐通告
     * @description 在坐标为 X,Y 的领土上，施展政令。该政令将驱逐特定数量的居民，并为背井离乡者提供一定的遣散费用（每人/5金钱或3食物）。
     * @method post
     * @url https://{url}/lord/policy/deported/open
     * @param x 必选 int X坐标
     * @param y 必选 int Y坐标
     * @param costType 必选 int 支付的资源类型（1：金钱、2：食物）
     * @param number 必选 int 被驱逐居民的数量
     * @return 1547489763
     * @return_param - int|string 政令完成的时间戳或失败原因
     * @number 50
     */
    public function openDeported(Request $request)
    {
        $number = (int)$request['number'];
        $costName = false;
        $costNumber = 0;
        if ($request['costType'] == 1) {
            $costName = 'money';
            $costNumber = $number * 5;
        } elseif ($request['costType'] == 2) {
            $costName = 'food';
            $costNumber = $number * 3;
        }

        if (!$number || !$costName) {
            return response('参数错误', 400);
        }
        // 扣除资源
        $resource = Resource::where('userId', Auth::id())->first();
        if ($resource->$costName < $costNumber) {
            return response('大爷，咱们的钱不够啦！', 400);
        }
        $coordinate = explode(',', Auth::user()->capital);
        $resource->$costName -= $costNumber;
        DB::beginTransaction();
        try {
            // 检查施政状态
            $policy = Policy::where(['x' => $coordinate[0], 'y' => $coordinate[1], 'policiesKey' => Policy::POLICIES_DEPORTED['id'], 'status' => Policy::STATUS['doing']])->first();
            if ($policy) {
                $endInfo = '政策“' . Policy::POLICIES_TRANS[2]['name'] . '”结束了，我们狠心地驱逐了' . $policy->tips . '位居民。';
                $check = (new Policy())->getStatus(Policy::POLICIES_DEPORTED['id'], Auth::id(), $coordinate[0], $coordinate[1], $endInfo);
                if (is_numeric($check)) {
                    return '政策实施中，请勿重复施政';
                }
            }

            if (!$resource->save()) {
                return response(500, '支付无法保存');
            }

            // 启动政策
            $endTime = Policy::POLICIES_DEPORTED['time'] + $_SERVER['REQUEST_TIME'];
            $result = (new Policy())->addWithMe(Policy::POLICIES_DEPORTED['id'], $coordinate[0], $coordinate[1], $endTime, $number);

            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }
            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }

    /**
     * showdoc
     * @catalog 前后端接口/领地
     * @title [动作]终止居民驱逐通告
     * @description 直接结束招募，但不退还发执行政令的费用
     * @method get
     * @url https://{url}/lord/policy/deported/stop/{x}/{y}
     * @param x 必选 int X坐标
     * @param y 必选 int Y坐标
     * @return 政策已终止
     * @return_param - string 提示语
     * @number 50
     */
    public function stopDeported(int $x, int $y)
    {
        // 启动政策
        $coordinate = explode(',', Auth::user()->capital);
        DB::beginTransaction();
        try {
            $result = (new Policy())->stopWithMe(Policy::POLICIES_DEPORTED['id'], $coordinate[0], $coordinate[1]);
            if (is_array($result) && $result['status'] !== 200) {
                DB::rollBack();
                return response($result['info'], $result['status']);
            }

            DB::commit();
            return $result;
        } catch (\Exception $exception) {
            DB::rollBack();
            return response($exception->getMessage(), 500);
        }
    }
}
