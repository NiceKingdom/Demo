<?php

namespace App\Console;

use App\Models\Log;
use App\Mail\AirQualityWarning;
use Illuminate\Support\Facades\Mail;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        // 每小时输出一句激励向格言
//        $schedule->command('inspire')
//            ->hourly();

        // 调用
        $schedule->call($this->getAirQuality())->daily();
    }

    /**
     * 获取空气质量
     * 空气质量触发警报值时，发送警告邮件
     */
    protected function getAirQuality()
    {
        // 全局设定
        $url = 'http://api.waqi.info/search/';
        $token = '8544e82629bc63a26add4b31a536721d363338e5';
        $lowLimit = 125;  // http://aqicn.org/scale/cn/
        $highLimit = 200;  // http://aqicn.org/scale/cn/

        // 自定义设定
        $cityToUsers = [
            'kaifeng' => [
                [
                    'prefix' => '亲爱的',
                    'nickname' => '妈妈',
                    'email' => '1728429616@qq.com',
                    'blessed' => '',
                ],
            ],
            'beijing' => [
                [
                    'prefix' => '',
                    'nickname' => 'UioSun',
                    'email' => 'uiosun@outlook.com',
                    'blessed' => '',
                ],
                [
                    'prefix' => '可爱的',
                    'nickname' => '杨航小宝贝',
                    'email' => '752453143@qq.com',
                    'blessed' => '',
                ],
            ],
        ];
        $citysGEO = [  // https://lbs.qq.com/tool/getpoint/index.html
            'beijing' => [
                'ws' => [39.84, 116.26],  // min
                'en' => [40.01, 116.49],  // max
            ],
            'kaifeng' => [
                'ws' => [34.74, 114.24],  // min
                'en' => [34.82, 114.40],  // max
            ],
        ];
        $cityTrans = ['beijing' => '北京', 'kaifeng' => '开封'];

        // 轮询
        foreach ($cityToUsers as $city => $users) {
            // 获取污染量
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "$url?token=$token&keyword=$city");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = json_decode(curl_exec($ch), true);
            curl_close($ch);

            $errorInfo = false;
            if (!is_array($result)) {
                $errorInfo = '获取数据格式有误';
            } elseif ($result['status'] !== 'ok') {
                $errorInfo = $result['data'] ?? '未知的错误原因';
            }
            if ($errorInfo) {
                $logModel = new Log();
                $logModel->category = Log::CATEGORY['AQ'];
                $logModel->status = Log::DEFAULT_ERROR;
                $logModel->info = $errorInfo;
                $logModel->localization = '-';
                $logModel->userId = 0;
                $logModel->uri = 'UserController::getAirQuality';
                $logModel->ip = '-';
                return ;
            }
            $result = $result['data'];

            // 获取平均值、判定是否要报警 >= $limit
            $average = [
                'number' => 0,
                'times' => 0,
            ];
            foreach ($result as $item) {
                if (!is_numeric($item['aqi'])
                    || $item['station']['geo'][0] < $citysGEO[$city]['ws'][0]
                    || $item['station']['geo'][1] < $citysGEO[$city]['ws'][1]
                    || $item['station']['geo'][0] > $citysGEO[$city]['en'][0]
                    || $item['station']['geo'][1] > $citysGEO[$city]['en'][1]
                ) {
                    continue;
                }
                $average['number'] += $item['aqi'];
                $average['times']++;
            }

            $ave = 0;
            if ($average['times'] > 0) {
                $ave = $average['number'] / $average['times'];
            }

            if ($ave > $lowLimit) {
                foreach ($users as $user) {
                    $user['pm2.5'] = $ave;
                    $user['city'] = $cityTrans[$city];

                    // 发送邮件
                    Mail::to($user['email'])->send(new AirQualityWarning($user));
                }
            }
        }
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
