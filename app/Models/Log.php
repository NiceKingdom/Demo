<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Log
 *
 * @package Log
 * @property string info 日志信息
 * @property int status 状态码或错误码
 * @property string category 日志类型
 * @property string localization 发生位置
 * @property int userId 用户 ID
 * @property string uri URI
 * @property string ip 用户 IP，仅供参考
 */
class Log extends Model
{
    public const DEFAULT_SUCCESS = 200;
    public const DEFAULT_ERROR = 500;

    public const CATEGORY = [
        'AQ' => 'AirQuality',
    ];
}
