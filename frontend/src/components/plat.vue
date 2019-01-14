<template>
  <div>
    <img class="logo" src="../assets/logo.png" alt="">
    <br>
    <p class="remarks">(～￣▽￣)～ 该页面功能暂未开放，尽请期待！</p>
    <br>
    <div class="test">- - - - Example - - - -</div>
    <div class="all-father">
      <div class="item-2">2-cell</div>
      <div class="item-5">5-cell</div>
      <div class="item-12">12-cell</div>
      <div class="item-24">24-cell</div>
   </div>

    <br>
    <button class="orange-button" @click="test">点我改变长度</button>
    <div class="progress">
      <div class="loader-container" v-bind:style="styleObject"></div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'plat',
    // 地图页面
    data () {
      return {
        styleObject: {
          width: '89%'
        },
      }
    },
    methods: {
      test: function () {
        this.styleObject.width = this.styleObject.width === '89%' ? '12%' : '89%'
      },
    }
  }
</script>

<style scoped lang="scss">
  @import "../assets/scss/global_css";
  .test {
    color: #1fa492;
  }
  .remarks {
    color: #1fa492;
    font-weight: bold;
  }
  .progress{
    width: 70%;
    height: 7px;
    border-radius:7px;
    margin-left: 15%;
    margin-top: 20%;
    background: -webkit-linear-gradient(left,#e4e3e4,#e4e5e4);
    background: -moz-linear-gradient(left,#e4e3e4,#e4e5e4);
    background: -o-linear-gradient(left,#e4e3e4,#e4e5e4);
    background: -ms-linear-gradient(left,#e4e3e4,#e4e5e4);
    background: linear-gradient(left,#e4e3e4,#e4e5e4);
  }
  .loader-container {
    height: 100%;
    /*width: 30%;*/
    background: -webkit-linear-gradient(left,#f8dc4b,#d95a3d);
    background: -moz-linear-gradient(left,#f8dc4b,#d95a3d);
    background: -o-linear-gradient(left,#f8dc4b,#d95a3d);
    background: -ms-linear-gradient(left,#f8dc4b,#d95a3d);
    background: linear-gradient(left,#f8dc4b,#d95a3d);
    border-radius:7px;
    box-shadow: inset 0 -2px 2px rgba(0, 0, 0, 0.2);
    animation: loader 1s linear;
  }
</style>
