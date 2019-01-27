<?php
namespace App\Http\Controllers\Common;

use App\Http\Controllers\Controller;
use App\Models\Building;
use App\Models\BuildingList;
use App\Models\Resource;
use App\Models\System;
use App\Service\LogService;
use App\User;
use App\Service\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userService;
    protected $logService;

    public function __construct(UserService $userService, LogService $logService)
    {
        $this->userService = $userService;
        $this->logService = $logService;
    }

    /**
     * showdoc
     * @catalog 前后端接口/用户
     * @title [获取]登录状态
     * @description 获取用户的登录状态
     * @method get
     * @url https://{url}/index
     * @return {"isLogin":false,"time":1546190299}
     * @return_param isLogin bool 是否已登录
     * @return_param time int 服务器时间（一般源于北京授时中心）
     * @number 20
     */
    public function getStatus()
    {
        return ['isLogin' => Auth::check(), 'time' => nowTime()];
    }

    /**
     * showdoc
     * @catalog 前后端接口/用户
     * @title [动作]用户注册
     * @description -
     * @method post
     * @url https://{url}/register
     * @param email 必选 string 邮箱，也是用户名
     * @param password 必选 string 密码
     * @param nickname 必选 string 用户的昵称
     * @param kingdom 必选 string 用户的王国名称
     * @return {"user":{"id":34,"nickname":"\u9488\u7ec7\u7684\u4eba","kingdom":"\u9ea6\u514b\u6069\u5c0f\u56fd","capital":"2,101","created_at":"2018-12-26 16:09:36","updated_at":"2018-12-26 16:09:36"},"system":{"id":1,"gameTime":4669441,"pack":"\u521b\u4e16","version":0.01},"resource":{"id":1,"userId":34,"people":199,"peopleChip":0,"peopleOutput":0,"food":3000,"foodChip":0,"foodOutput":0,"wood":1990,"woodChip":0,"woodOutput":0,"stone":1000,"stoneChip":0,"stoneOutput":0,"money":781066,"moneyChip":0.5,"moneyOutput":0,"area":200,"areaChip":0,"areaOutput":0,"created_at":"2018-12-26 16:09:36","updated_at":"2018-12-27 13:52:11"},"building":{"id":2,"userId":34,"farm01":0,"farm02":0,"sawmill01":0,"sawmill02":0,"created_at":"2018-12-26 16:09:36","updated_at":"2018-12-26 16:09:36"}}
     * @return_param user array 用户基本信息
     * @return_param user[id] int 用户ID
     * @return_param user[nickname] int 昵称
     * @return_param user[kingdom] int 领地名
     * @return_param user[capital] int 领地主坐标
     * @return_param user[created_at] int 创建时间
     * @return_param user[updated_at] int 更新时间
     * @return_param system array 系统当前信息
     * @return_param system[gameTime] int 游戏时间戳
     * @return_param system[pack] string 当前版本的昵称
     * @return_param system[version] string 版本号
     * @return_param resource array 资源数据，详见“资源/[获取]用户的资源”接口
     * @return_param building array 拥有的建筑数量
     * @return_param building[id] int 建筑ID
     * @return_param building[userId] int 用户ID
     * @return_param building[farm01] int 农田数量（1级）
     * @return_param building[farm02] int 农田数量（2级）
     * @return_param building[sawmill01] int 伐木场数量（1级）
     * @return_param building[BUILDING_NAME_LEVEL] int 后续建筑按上述规则延续
     * @return_param building[created_at] int 创建时间
     * @return_param building[updated_at] int 更新时间
     * @number 10
     */
    public function register(Request $request)
    {
        $check = User::where('email', $request['email'])->exists();
        if ($check) {
            return response('帐号已存在，找回它，或换一个吧', 400);
        }

        $userPlant = config('params.userPlant');
        $lastId = User::select('id')->orderby('id', 'desc')->first() ?? new User();
        $id = $lastId->id + 1;
        $capital = ceil($id / sqrt($userPlant)) * 3 - 1 . ',' . ((($id - 1) % sqrt($userPlant) + 1) * 3 - 1);

        DB::beginTransaction();
        try {
            $info['user'] = User::create([
                'nickname' => $request['nickname'],
                'email' => $request['email'],
                'kingdom' => $request['kingdom'],
                'capital' => $capital,
                'password' => Hash::make($request['password']),
            ]);

            $buildingList = [
                'userId' => $info['user']->id,
                'startTime' => 0,
                'endTime' => 0,
                'type' => '',
                'level' => 0,
                'action' => BuildingList::ACTION_SLEEP,
                'number' => 0,
            ];
            BuildingList::create($buildingList);
            BuildingList::create($buildingList);
            BuildingList::create($buildingList);

            $this->logService::signUpOrIn('注册成功', 101, false);

            $info['user'] = User::find($info['user']->id);

            Building::create([
                'userId' => $info['user']->id,
            ]);
            Resource::create([
                'userId' => $info['user']->id,
            ]);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $logID = 'UriBT' . logService::common('注册失败:' . $exception->getMessage(), 500, 'Common\UserController::register', 'Error');
            return response('意外情况，编号：' . $logID, 500);
        }

        if (Auth::check() || Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            $this->userService->checkRedis();
            $userInfo = $this->getUserInfo(Auth::id());

            return $userInfo;
        }

        return response('服务器出现意外情况，请尝试手动登录', 500);
    }

    /**
     * showdoc
     * @catalog 前后端接口/用户
     * @title [动作]用户登录
     * @description -
     * @method post
     * @url https://{url}/login
     * @param email 必选 string 邮箱
     * @param password 必选 string 密码
     * @return {"user":{"id":26,"name":"\u533f\u540d","nickname":"\u4e00\u4e2a\u65e0\u804a\u7684\u4eba","email":"UioSun@163.com","kingdom":"\u56fe\u72e9\u738b\u671d","capital":"2,77","created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"},"system":{"id":1,"gameTime":6713622,"pack":"\u521b\u4e16","version":0.01,"created_at":"2018-08-25 15:55:49","updated_at":"2018-08-25 15:55:49"},"resource":{"id":26,"userId":26,"people":200,"peopleChip":0,"peopleOutput":0,"food":3000,"foodChip":0,"foodOutput":0,"wood":2000,"woodChip":0,"woodOutput":0,"stone":1000,"stoneChip":0,"stoneOutput":0,"money":3500,"moneyChip":0,"moneyOutput":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"},"building":{"id":26,"userId":26,"farm01":0,"farm02":0,"sawmill01":0,"sawmill02":0,"created_at":"2018-11-05 00:06:21","updated_at":"2018-11-05 00:06:21"}}
     * @return_param user array 用户基本信息
     * @return_param user[id] int 用户ID
     * @return_param user[nickname] int 昵称
     * @return_param user[kingdom] int 领地名
     * @return_param user[capital] int 领地主坐标
     * @return_param user[created_at] int 创建时间
     * @return_param user[updated_at] int 更新时间
     * @return_param system array 系统当前信息
     * @return_param system[gameTime] int 游戏时间戳
     * @return_param system[pack] string 当前版本的昵称
     * @return_param system[version] string 版本号
     * @return_param resource array 资源数据，详见“资源/[获取]用户的资源”接口
     * @return_param building array 拥有的建筑数量
     * @return_param building[id] int 建筑ID
     * @return_param building[userId] int 用户ID
     * @return_param building[farm01] int 农田数量（1级）
     * @return_param building[farm02] int 农田数量（2级）
     * @return_param building[sawmill01] int 伐木场数量（1级）
     * @return_param building[BUILDING_NAME_LEVEL] int 后续建筑按上述规则延续
     * @return_param building[created_at] int 创建时间
     * @return_param building[updated_at] int 更新时间
     * @number 50
     */
    public function login(Request $request)
    {
        // 若存在登录状态，则先注销
        if (Auth::check()) {
            Auth::logout();
            Session::flush();
        }

        if (Auth::attempt(['email' => $request['email'], 'password' => $request['password']])) {
            $this->logService::signUpOrIn('登录成功', 101);

            $this->userService->checkRedis();
            $userInfo = $this->getUserInfo(Auth::id());

            return $userInfo;
        }

        return response('帐号或密码错误，请检查后重试, ', 400);
    }

    /**
     * 获取用户的全部信息（已脱敏）
     *
     * @param int $userId
     * @return bool
     */
    protected function getUserInfo($userId = 0)
    {
        if (!$userId) {
            if (!Auth::check()) {
                return false;
            }
            $userId = Auth::id();
        }

        $info['user'] = User::find($userId);
        unset($info['user']['name']);
        unset($info['user']['email']);

        if (!$systemInfo = Redis::get('systemInfo')) {
            $systemInfo = json_encode(System::orderBy('id', 'desc')->first());
            Redis::set('systemInfo', $systemInfo);
        }
        $info['system'] = json_decode($systemInfo);
        unset($info['system']->created_at);
        unset($info['system']->updated_at);

        $info['resource'] = Resource::where('userId', $userId)->first();
        $info['building'] = Building::where('userId', $userId)->first();

        return $info;
    }

    /**
     * showdoc
     * @catalog 前后端接口/用户
     * @title [动作]用户注销
     * @description -
     * @method get
     * @url https://{url}/logout
     * @return Site home page in outside
     * @number 99
     */
    public function logout()
    {
        Auth::logout();
        Session::flush();

        return redirect('/');
    }
}
