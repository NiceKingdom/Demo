<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BuildingTest extends TestCase
{
    public function login()
    {
        $this->json('POST', '/login', [
            'email' => 'UioSun@163.com',
            'password' => '11111111',
        ]);
    }

    public function testBuildingList()
    {
        $this->login();
        $response = $this->json('GET', '/building/list');

        $response->assertStatus(200);
    }

    public function testSchedule()
    {
        $this->login();
        $response = $this->json('GET', '/building/schedule');

        $response->assertStatus(200);
    }

    public function testBuild()
    {
        $this->login();
        $response = $this->json('POST', '/building/build', [
            'type' => 'farm',
            'level' => '1',
            'number' => '5',
        ]);

        $response->assertStatus(200);
    }

    public function testDestroy()
    {
        $this->login();
        $response = $this->json('POST', '/building/destroy', [
            'type' => 'farm',
            'level' => '1',
            'number' => '3',
        ]);

        $response->assertStatus(200);
    }

    public function testRecall()
    {
        $this->login();
        $response = $this->json('POST', '/building/recall', [
            'id' => 76,
        ]);

        $response->assertStatus(200);
    }
}
