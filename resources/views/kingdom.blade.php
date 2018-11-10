@extends('layouts.app')

@section('title', '王国')
@section('content')
    @verbatim
        <el-row type="flex" justify="center">
            <el-col :offset="3" :span="10">
                <ul class="none-list">
                    <li><span class="color-blue">时间</span>：{{gameTime}}</li>
                    <li><span class="color-blue">王国</span>：{{user.nickname}}的<span class="color-red">{{user.kingdom}}</span></li>
                </ul>
            </el-col>
            <el-col :span="10">
                <el-col :span="8">
                    <ul class="none-list">
                        <li><span class="color-blue">王国资源 - 居民</span>：{{resource.people}}</li>
                        <li><span class="color-gold">王国资源 - 货币</span>：{{resource.money}}</li>
                        <li><span class="color-green">王国资源 - 食物</span>：{{resource.food}}</li>
                        <li><span class="color-brown">王国资源 - 木材</span>：{{resource.wood}}</li>
                        <li><span class="color-gray">王国资源 - 石料</span>：{{resource.stone}}</li>
                    </ul>
                </el-col>
                <el-col :span="12">
                    <ul class="none-list">
                        <li><span class="color-blue">增量</span>：无法计算</li>
                        <li><span class="color-gold">增量</span>：{{resource.moneyOutput}}</li>
                        <li><span class="color-green">增量</span>：{{resource.foodOutput}}</li>
                        <li><span class="color-brown">增量</span>：{{resource.woodOutput}}</li>
                        <li><span class="color-gray">增量</span>：{{resource.stoneOutput}}</li>
                    </ul>
                </el-col>
            </el-col>
        </el-row>
    @endverbatim
@endsection

@section('js')
    <script>
      let data = JSON.parse(localStorage.getItem('user'))
      data.activeHeader = 'kingdom'
    </script>
@endsection
