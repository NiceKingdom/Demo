<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoliciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId'); # 用户 ID，仅作为锚定，地点被占领后，不影响政策实行
            $table->unsignedInteger('x'); # X 坐标
            $table->unsignedInteger('y'); # Y 坐标
            $table->unsignedMediumInteger('policiesKey'); # 政策编号
            $table->string('title', 30); # 标题
            $table->unsignedInteger('endTime'); # 结束时间
            $table->string('tips', 1000)->nullable(); # 备注，闲置字段，供复杂功能使用
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
        Schema::dropIfExists('policies');
    }
}
