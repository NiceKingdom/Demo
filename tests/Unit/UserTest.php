<?php

namespace Tests\Unit;

use App\Http\Controllers\Common\UserController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function testRegister()
    {
        exec('php artisan migrate:refresh --seed');
        exec('echo "" > storage/logs/laravel.log');

        $response = $this->withHeaders([
            'Referer' => 'http://www.nice-kingdom.uio/register',
        ])->json('POST', '/register', [
            'nickname' => '一个无聊的人',
            'email' => 'UioSun@163.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
            'kingdom' => '大不列颠王朝',
        ]);

        $response->assertStatus(200);
    }

    public function testLogin()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'UioSun@163.com',
            'password' => '11111111',
        ]);

        $response->assertStatus(200);
    }
}
