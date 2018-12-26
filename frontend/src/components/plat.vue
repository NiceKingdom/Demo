<template>
  <div>
    <img class="logo" src="../assets/logo.png" alt="">
    <br/>
    <div class="test">- - - - Example - - - -</div>
    <div class="all-father">
      <div class="item-2">2-cell</div>
      <div class="item-5">5-cell</div>
      <div class="item-12">12-cell</div>
      <div class="item-24">24-cell</div>
   </div>
  </div>
</template>

<script>
  export default {
    name: 'plat',
    // 地图页面
    data () {
      return {}
    },
  }
</script>

<style scoped lang="scss">
  @import "../assets/scss/mycss_lib";
  .test {
    color: #1fa492;
  }
  .all-father {
    @include father-child-test;
  }
</style>
