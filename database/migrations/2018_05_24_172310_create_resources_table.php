<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('userId');

            $table->unsignedInteger('people')->default(100); # 人口 - 成人
            $table->float('peopleChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('peopleOutput')->default(0); # 实际产出

            $table->unsignedInteger('food')->default(3000); # 食物(Max: 40 亿)
            $table->float('foodChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('foodOutput')->default(240); # 实际产出

            $table->unsignedInteger('wood')->default(2000); # 木材
            $table->float('woodChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('woodOutput')->default(0); # 实际产出

            $table->unsignedInteger('stone')->default(1000); # 石块
            $table->float('stoneChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('stoneOutput')->default(0); # 实际产出

            $table->unsignedInteger('money')->default(3500); # 钱财
            $table->float('moneyChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('moneyOutput')->default(0); # 实际产出

            $table->unsignedInteger('area')->default(200); # 面积
            $table->float('areaChip', 5, 4)->default(0); # 散碎
            $table->mediumInteger('areaOutput')->default(0); # 实际产出
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
        Schema::dropIfExists('resources');
    }
}
