<?php

namespace Tests\Unit;

use App\User;
use Tests\TestCase;

class BuildingTest extends TestCase
{
    /**
     * 获取建筑列表
     */
    public function testBuildingList()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/building/list');

        $response->assertStatus(200);
    }

    /**
     * 获取建筑进程（无）
     */
    public function testSchedule()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    /**
     * 建筑开始
     */
    public function testBuild()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('POST', '/building/build', [
                'type' => 'farm',
                'level' => '1',
                'number' => '5',
            ]);

        $response->assertStatus(200);
    }

    /**
     * 获取建筑进程（一个）
     */
    public function testScheduleHasOne()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    /**
     * 建筑拆除
     */
    public function testDestroy()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('POST', '/building/destroy', [
                'type' => 'farm',
                'level' => '1',
                'number' => '3',
            ]);

        $response->assertStatus(200);
    }

    /**
     * 获取建筑进程（两个）
     */
    public function testScheduleHasZero()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    /**
     * 建筑取消拆除
     */
    public function testRecall()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('POST', '/building/recall', [
                'id' => 76,
            ]);

        $response->assertStatus(200);
    }

    /**
     * 获取建筑进程（一个）
     */
    public function testScheduleHasOneAgain()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }
}
