<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ElementUI --}}
    <link href="https://cdn.bootcss.com/element-ui/2.4.0/theme-chalk/index.css" rel="stylesheet">

    <title>繁盛王国 - @yield('title')</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="{{ asset('css/app.css?v='.time()) }}">
</head>
<body>
<div id="app">
    <el-container>
        <el-header>
            @if (\Illuminate\Support\Facades\Auth::check()) @verbatim
                <el-row>
                    <el-col :offset="2" :span="20">
                        <el-menu
                                :default-active="activeHeader"
                                class="el-menu-demo"
                                mode="horizontal"
                                @select="handleSelect"
                                background-color="#545c64"
                                text-color="#fff"
                                active-text-color="#ffd04b">
                            <el-menu-item index="kingdom">国度</el-menu-item>
                            <el-menu-item index="building"><a href="building">施工</a></el-menu-item>
                        </el-menu>
                    </el-col>
                </el-row>
            @endverbatim @else
                @verbatim
                    <img src="#" alt="Logo"> <br />
                @endverbatim
            @endif
        </el-header>
        <el-main>
            @yield('content')
        </el-main>
        @verbatim
            <el-footer>
                <el-row>
                    <el-col :span="24">
                        「{{site.name}}」© {{ site.start }} - {{ site.now }}
                        <br>{{ site.record }}
                    </el-col>
                </el-row>
            </el-footer>
    </el-container>
</div>
@endverbatim

{{-- VueJS --}}
<script src="https://cdn.bootcss.com/vue/2.5.17-beta.0/vue.min.js"></script>
{{-- ElementUI --}}
<script src="https://cdn.bootcss.com/element-ui/2.4.0/index.js"></script>
{{-- Vue-Resource --}}
<script src="https://cdn.bootcss.com/vue-resource/1.5.1/vue-resource.min.js"></script>
{{-- SweetAlert --}}
<script src="https://cdn.bootcss.com/sweetalert/2.1.0/sweetalert.min.js"></script>
<script src="js/global.js"></script>
@yield('js')
<script>
  // 获取可能存在的属性
  let config = {
    el: '#app',
    data: data,
  }
  if (typeof methods !== "undefined") {
    config.methods = methods
  } else {
    config.methods = {}
  }
  if (typeof computed !== "undefined") {
    config.computed = computed
  } else {
    config.computed = {}
  }
  // 赋值全局属性
  config.data.site = {
    name: '自由小岛',
    start: 2017,
    now: new Date().getFullYear(),
    record: '萌ICP备 66666666号-1',
  }

  // 赋值全局方法
  config.methods.get = function(url, alert, func) {
    this.$http.get(url).then(response => {
      response = response.body
      if (alert !== '') {
        swal({
          text: (alert === 'sayResponse') ? response[1] : alert,
          type: 'success',
        })
      }
      if (func) {
        func(response)
      }
    }, response => {
      swal({
        text: '∑( ° △ ° 服，服务器溜啦！',
        type: 'error',
      })
    })
  }
  config.methods.post = function(url, data, alert, func) {
    this.$http.post(url, data).then(response => {
      console.info(response)
      response = response.body
      console.info(response)
      if (alert !== '') {
        swal({
          text: (alert === 'sayResponse') ? response[1] : alert,
          type: 'success',
        })
      }
      if (func) {
        func(response)
      }
    }, response => {
      swal({
        text: '∑( ° △ ° 服，服务器溜啦！',
        type: 'error',
      })
    })
  }

  config.methods.handleSelect = function(key, keyPath) {
    localStorage.setItem('user', JSON.stringify(this.$data))
    window.location.href = key
  }

  config.methods.systemUp = function () {
    this.system.gameTime++
    // console.log('时间流转: ' + this.system.gameTime)
  }

  config.computed.gameTime = function () {
    let year = 1
    let month = 1
    let day = Math.floor(this.system.gameTime / 15)

    if (day >= 360) {
      year += Math.floor(day / 360)
      day %= 360
    }
    if (day >= 30) {
      month += Math.floor(day / 30)
      day %= 30
    }

    return year + '年' + month + '月' + day + '日'
  }

  // 赋值全局生命周期事件（created）
  config.created = function () {
    setInterval(this.systemUp, 1000)
    this.handleCreated()
  }

  let app = new Vue(config)
</script>
</body>
</html>
