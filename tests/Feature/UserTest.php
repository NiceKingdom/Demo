<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * 用户注册
     */
    public function testRegister()
    {
        // Mock
        require_once 'testHelper.php';
        initDevelopment();

        // Logic
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

    /**
     * 用户重复注册
     */
    public function testRegisterAgain()
    {
        $response = $this->withHeaders([
            'Referer' => 'http://www.nice-kingdom.uio/register',
        ])->json('POST', '/register', [
            'nickname' => '一个无聊的人',
            'email' => 'UioSun@163.com',
            'password' => '11111111',
            'password_confirmation' => '11111111',
            'kingdom' => '大不列颠王朝',
        ]);

        $response->assertStatus(400);
        $response->assertSeeText('帐号已存在，找回它，或换一个吧');
    }

    /**
     * 用户登录
     */
    public function testLogin()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'UioSun@163.com',
            'password' => '11111111',
        ]);

        $response->assertStatus(200);
    }

    /**
     * 获取登录状态（登陆中）
     */
    public function testIndex()
    {
        $user = User::find(26);
        $response = $this->actingAs($user)
            ->json('GET', '/index');

        $response->assertStatus(200);
        $response->assertSeeText('true');
    }

    /**
     * 用户错误号码登录
     */
    public function testLoginFailedEmail()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'UioSun@pipi.com',
            'password' => '11111111',
        ]);

        $response->assertStatus(400);
        $response->assertSeeText('帐号或密码错误，请检查后重试');
    }

    /**
     * 用户错误密码登录
     */
    public function testLoginFailedPass()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'UioSun@163.com',
            'password' => '12iugi245',
        ]);

        $response->assertStatus(400);
        $response->assertSeeText('帐号或密码错误，请检查后重试');
    }

    /**
     * 用户注销
     */
    public function testLogout()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'UioSun@163.com',
            'password' => '11111111',
        ]);

        $response->assertStatus(200);
    }

    /**
     * 获取登录状态（注销时）
     */
    public function testIndexLogout()
    {
        $response = $this->json('GET', '/index');

        $response->assertStatus(200);
        $response->assertSeeText('false');
    }
}
