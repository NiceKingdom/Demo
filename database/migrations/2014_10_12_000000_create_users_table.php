<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 36)->default('匿名'); # 称呼
            $table->string('nickname', 16); # 领主名
            $table->string('email')->unique(); # 邮箱（登录名）
            $table->string('password', 64); # 密码
            $table->string('kingdom', 12); # 王国名
            $table->string('capital', 12); # 首都
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}

/* Perlin Noise Test */

