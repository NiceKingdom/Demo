<?php

namespace App\Service;

use App\Models\Log;
use App\User;
use Illuminate\Support\Facades\Redis;

class LogService
{
    /**
     * 增加一条日志
     *
     * @param string $info 日志信息
     * @param int $status return 的状态码或错误码
     * @param string $category 类型
     * @param string $localization 错误位置（最少具体到方法名）
     * @param bool $userId 用户 ID，不制定则为 Auth 值
     * @return bool|int
     */
    protected function setLog($info, $status, $category, $localization, $userId = NULL)
    {
        $userId = $userId ?? User::getUserId();

        $log = new Log();
        $log->info = $info;
        $log->status = $status;
        $log->category = $category;
        $log->localization = $localization;
        $log->userId = $userId;
        $log->uri = addslashes($_SERVER['REQUEST_URI'] ?? '未截取到 REQUEST_URI');
        $log->ip = $_SERVER['REMOTE_ADDR'] ?? '未截取到 REMOTE_ADDR';

        if ($log->save()) {
            return $log->id;
        }

        return false;
    }

    /**
     * 通用日志接口
     *
     * @param $info
     * @param $status
     * @param $local
     * @param bool $category
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function common($info, $status, $local, $category = false)
    {
        $category = $category ?? 'Common';

        $model = new self();
        $logId = $model->setLog($info, $status, $category, $local);
        if ($logId) {
            return $logId;
        }

        return response('日志录入失败，位置：' . $local, 500);
    }

    /**
     * 增加一条登录或注册的日志
     *
     * @param $info
     * @param $status
     * @param bool $isLogin
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function signUpOrIn($info, $status, $isLogin = true)
    {
        $model = new self();
        $local = ($isLogin) ? 'login' : 'register';
        if ($model->setLog($info, $status, 'User', $local)) {
            return true;
        }

        return response('日志录入失败，位置：login.', 500);
    }

    /**
     * 增加一条管理日志
     *
     * @param $info
     * @param $status
     * @param $local
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function admin($info, $status, $local)
    {
        $model = new self();
        if ($model->setLog($info, $status, 'Admin', $local)) {
            return true;
        }

        return response('日志录入失败，位置：' . $local, 500);
    }

    /**
     * 增加用于追踪的临时日志
     * todo: 有管理后台后，将本方法及清除方法的逻辑，更正至 Redis
     *
     * @param $info
     * @param $status
     * @param $local
     * @return bool|\Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    static function track($info, $status, $local)
    {
        $model = new self();
        if ($model->setLog($info, $status, 'Track', $local)) {
            return true;
        }

        return response('日志录入失败，位置：' . $local, 500);
    }

    /**
     * 清除临时日志
     *
     * @return mixed
     */
    static function trackClear()
    {
        return Log::where('category', 'Track')->delete();
    }
}
