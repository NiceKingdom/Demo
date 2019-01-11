<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class ResourcePolicyTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        // Mock
        $i = new self();
        $i->json('GET', '/reset/redis');
        require_once 'testHelper.php';
        initDevelopment();
        setBuildingListOnInstant();
    }

    /**
     * 终止政令（不存在）
     */
    public function testBuildingList()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('GET', '/building/list');

        $response->assertStatus(200);
    }

    /**
     * 获取政令进度（不存在）
     */
    public function testSchedule()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    /**
     * 启动政令
     */
    public function testBuild()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('POST', '/building/build', [
                'type' => 'farm',
                'level' => '1',
                'number' => '5',
            ]);

        $response->assertStatus(200);
    }

    /**
     * 获取政令进度（未完成）
     */
    public function testScheduleHasOne()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    /**
     * 终止政令（存在）
     */
    public function testDestroy()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('POST', '/building/destroy', [
                'type' => 'farm',
                'level' => '1',
                'number' => '3',
            ]);

        $response->assertStatus(200);
    }

    /**
     * 启动两次政令（前者尚未执行完毕）
     */
    public function testDestroy2()
    {
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('POST', '/building/destroy', [
                'type' => 'farm',
                'level' => '1',
                'number' => '3',
            ]);

        $response->assertStatus(200);
    }

    /**
     * 获取政令进度（完成）
     */
    public function testScheduleHasOneAgain()
    {
        // Logic
        $user = User::find(1);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    static public function tearDownAfterClass()
    {
        $i = new self();
        $i->assertTrue(true);
        $i->json('GET', '/reset/redis');
    }
}
