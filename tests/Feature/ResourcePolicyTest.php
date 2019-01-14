<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

// TODO: 尝试改写 time
function time($timestamp = 0) {
    return $timestamp ?: \time();
}

class ResourcePolicyTest extends TestCase
{
    protected const USER_ID = 1;
    protected const RAY_X = 2;
    protected const RAY_Y = 2;

    public static function setUpBeforeClass()
    {
        // Mock
        require_once 'testHelper.php';
        initDevelopment();
    }

    /**
     * 终止政令（不存在）
     */
    public function testBuildingList()
    {
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/stop/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $response->assertSee('这片土地尚未施行该政策，或该政策已失效');
    }

    /**
     * 获取政令进度（不存在）
     */
    public function testSchedule()
    {
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/know/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $response->assertSee('这片土地尚未施行该政策');
    }

    /**
     * 启动政令
     */
    public function testBuild()
    {
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/open/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $this->assertTrue(is_numeric($response->getContent()));
    }

    /**
     * 获取政令进度（未完成）
     */
    public function testScheduleHasOne()
    {
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/know/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $this->assertTrue(is_numeric($response->getContent()));
    }

    /**
     * 终止政令（存在）
     */
    public function testDestroy()
    {
        // 第一次
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/stop/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $this->assertTrue(is_numeric($response->getContent()));

        // 第二次
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/stop/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $response->assertSee('被废止');
    }

    /**
     * 同时启动两次政令
     */
    public function testDestroy2()
    {
        $user = User::find(self::USER_ID);
        $response = $this->actingAs($user)
            ->json('GET', '/lord/policy/enlisting/stop/' . self::RAY_X . '/' . self::RAY_Y);

        $response->assertStatus(200);
        $response->assertSee('被废止');
    }
//
//    /**
//     * 获取政令进度（完成）
//     */
//    public function testScheduleHasOneAgain()
//    {
//        // Logic
//        $user = User::find(self::USER_ID);
//        $response = $this->actingAs($user)
//            ->json('GET', '/building/schedule');
//
//        $response->assertStatus(200);
//        $response->assertSee('位流浪人投靠我们');
//    }
}
