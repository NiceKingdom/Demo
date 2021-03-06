<?php

namespace App\Http\Controllers\Building;

use App\Http\Requests\BuildingPost;
use App\Models\Building;
use App\Models\BuildingList;
use App\Models\Resource;
use App\Service\BuildingService;
use App\Service\LogService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class BuildingController extends Controller
{
    protected $buildingService;
    protected $logService;
    protected $projects;

    public function __construct(LogService $logService, BuildingService $buildingService)
    {
        $this->buildingService = $buildingService;
        $this->logService = $logService;

        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $this->buildingService->init();
            return $next($request);
        });
    }

    public function index()
    {
        $info = [];

        if (Auth::check()) {
            $info['building'] = Building::find(Auth::id());
            $info['resource'] = Resource::where('userId', Auth::id())->first();
        }
        $info['list'] = json_decode(Redis::get('buildingList'), true);
        $info['schedule'] = $this->schedule();

        return $info;
    }

    /**
     * showdoc
     * @catalog 前后端接口/建筑
     * @title [获取]页面必需信息
     * @description 暂无
     * @method get
     * @url https://{url}/building/list
     * @return {"building":{"id":26,"userId":26,"farm01":0,"farm02":0,"sawmill01":0,"sawmill02":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"},"list":{"farm":[{"name":"\u4e00\u7ea7\u519c\u7530","level":1,"time":75,"product":{"food":40},"material":{"wood":10},"occupy":{"people":1}},{"name":"\u4e8c\u7ea7\u519c\u7530","level":2,"time":105,"product":{"food":48},"material":{"wood":13},"occupy":{"people":1}}],"sawmill":[{"name":"\u4e00\u7ea7\u4f10\u6728\u8425\u5730","level":1,"time":120,"product":{"wood":0.6},"material":{"money":10},"occupy":{"people":1}},{"name":"\u4e8c\u7ea7\u4f10\u6728\u8425\u5730","level":2,"time":165,"product":{"wood":1.6},"material":{"money":28},"occupy":{"people":2}}]}}
     * @return_param building array 拥有的建筑数量
     * @return_param building[id] int 建筑ID
     * @return_param building[userId] int 用户ID
     * @return_param building[farm01] int 农田数量（1级）
     * @return_param building[farm02] int 农田数量（2级）
     * @return_param building[sawmill01] int 伐木场数量（1级）
     * @return_param building[BUILDING_NAME_LEVEL] int 后续建筑按上述规则延续
     * @return_param building[created_at] int 创建时间
     * @return_param building[updated_at] int 更新时间
     * @return_param list array 建筑清单
     * @return_param list[farm] string 建筑类型，farm 就是农田
     * @return_param list[farm][name] string 建筑名
     * @return_param list[farm][level] int 建筑级别
     * @return_param list[farm][time] int 建造时间
     * @return_param list[farm][product] array 资源产出
     * @return_param list[farm][product] array 建造成本
     * @return_param list[farm][product] array 生产时占用的资源
     * @number 20
     */
    public function buildingList()
    {
        if (Auth::check()) {
            $list['building'] = Building::find(Auth::id());
        }
        $list['list'] = json_decode(Redis::get('buildingList'), true);

        return $list;
    }

    /**
     * showdoc
     * @catalog 前后端接口/建筑
     * @title [获取]检查建筑队列并执行
     * @description 返回建筑队的当前情况
     * @method get
     * @url https://{url}/building/schedule
     * @return [{"id":76,"userId":26,"name":"\u5efa\u7b51\u961f","startTime":0,"endTime":0,"type":"","level":0,"action":0,"number":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"},{"id":77,"userId":26,"name":"\u5efa\u7b51\u961f","startTime":0,"endTime":0,"type":"","level":0,"action":0,"number":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"},{"id":78,"userId":26,"name":"\u5efa\u7b51\u961f","startTime":0,"endTime":0,"type":"","level":0,"action":0,"number":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"}]
     * @return_param id int 施工队ID
     * @return_param userId int 所属用户的ID
     * @return_param name string 施工队名
     * @return_param startTime int 施工开始时间戳
     * @return_param endTime int 施工结束时间戳
     * @return_param type string 工程类型
     * @return_param level int 工程级别
     * @return_param action int 施工行为([-1 => '建筑队不存在', 0 => '空闲', 1 => '施工中', 2 => '拆除中'])
     * @return_param number int 工程数量
     * @number 20
     */
    public function schedule()
    {
        $list = $this->buildingService->schedules;

        return $list;
    }

    /**
     * showdoc
     * @catalog 前后端接口/建筑
     * @title [动作]新建建筑
     * @description 暂无
     * @method get
     * @url https://{url}/building/build
     * @param type 必选 string 建筑类型，最长 20 字符
     * @param level 必选 int 建筑级别，最小为 1
     * @param number 可选 int 建筑数量，默认且最小为 1
     * @return ["succeed", "施工队已经开始工作"]
     * @return_param 0 string 执行结果，返回succeed或failed
     * @return_param 1 string 提示语
     * @number 50
     */
    public function build(BuildingPost $post)
    {
        return $this->buildingService->buildBefore($post->type, $post->level, $post->number);
    }

    /**
     * showdoc
     * @catalog 前后端接口/建筑
     * @title [动作]取消建筑
     * @description 暂无
     * @method get
     * @url https://{url}/building/recall
     * @param id 必选 施工队 ID
     * @return ["succeed", "已取消该施工项目"]
     * @return_param 0 string 执行结果，返回succeed或failed
     * @return_param 1 string 提示语
     * @number 50
     */
    public function recall(int $id)
    {
        $aimKey = false;
        foreach ($this->buildingService->schedules as $key => $schedule) {
            if ($schedule->id === $id) {
                $aimKey = $key;
                if ($schedule->action !== BuildingList::ACTION_BUILD)
                    return ['failed', '施工队并不在建设中'];
            }
        }

        return ($aimKey !== false) ? $this->buildingService->buildRecall($aimKey) : ['failed', '我们没有发现你说的那支建筑队'];
    }

    /**
     * showdoc
     * @catalog 前后端接口/建筑
     * @title [动作]拆除建筑
     * @description 暂无
     * @method get
     * @url https://{url}/building/destroy
     * @param type 必选 string 建筑类型，最长 20 字符
     * @param level 必选 int 建筑级别，最小为 1
     * @param number 可选 int 建筑数量，默认且最小为 1
     * @return ["succeed", "拆除完成"]
     * @return_param 0 string 执行结果，返回succeed或failed
     * @return_param 1 string 提示语
     * @number 50
     */
    public function destroy(BuildingPost $post)
    {
        return $this->buildingService->destroyBefore($post->type, $post->level, $post->number);
    }
}
