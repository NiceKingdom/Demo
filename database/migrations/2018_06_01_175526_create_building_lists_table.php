<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBuildingListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('building_lists', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');

            $table->string('name', 8)->default('建筑队'); # 标识
            $table->unsignedInteger('startTime'); # 开始时间
            $table->unsignedInteger('endTime'); # 截至时间
            $table->string('type', 18); # 建筑类型
            $table->unsignedTinyInteger('level'); # 建筑级别
            $table->tinyInteger('action')->default(-1); # 操作类型，-1 为未开启，0 为空闲，1 为建筑，2 为拆除
            $table->unsignedSmallInteger('number'); # 改动数量

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
        Schema::dropIfExists('building_lists');
    }
}
