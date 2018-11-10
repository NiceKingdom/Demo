<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->increments('id');

            $table->string('info', 960); # 日志信息
            $table->smallInteger('status')->default(101); # 状态码或错误码
            $table->string('category', 22); # 日志类型
            $table->string('localization', 64); # 发生位置
            $table->unsignedInteger('userId'); # 用户 ID
            $table->string('uri', 200); # URI
            $table->string('ip', 128); # 用户 IP，仅供参考

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
        Schema::dropIfExists('logs');
    }
}
