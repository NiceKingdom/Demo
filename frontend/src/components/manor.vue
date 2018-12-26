<template>
  <div>
      <div class="building-wrapper">
      <div class="building-header">
        <div class="logo-wrapper">
          <img class="logo" src="../assets/logo.png" alt="">
        </div>
        <div class="resource-wrapper">
          <VResourceBar />
        </div>
        <div class="building-name" @click="jump('manor')">{{kingdom}}</div>
      </div>
      <div class="building-main-wrapper">
        <div class="info-wrapper">
          <div class="left">
            <VProgress v-for="item in schedules" :process="item" :key="item.id" />
          </div>
          <div class="right">
            <div class="building">
              <p>
                <span>建筑清单：</span>
                <span style="padding: 2px; margin: 2px; color: #636363; border: 1px solid black;"
                      v-for="type in buildType"
                      @click="toggle(type)"
                      :key="type">{{typeTrans(type)}}</span>
              </p>
              <label for="number">数量</label>
              <input type="number" id="number" v-model="actionNumber" class="btn-number">

              <table>
                <tr>
                  <th width="120">名称</th>
                  <th>拥有</th>
                  <th>时间</th>
                  <th>等级</th>
                  <th>占用</th>
                  <th>成本</th>
                  <th>产出</th>
                  <th>操作</th>
                </tr>
                <VBuildingList v-for="item in buildingList[activeType]" :key="item.name"
                               :building="item" @build="build" @destroy="destroy" />
              </table>
            </div>
          </div>
        </div>
        <div class="links-wrapper">
          <button class="link" @click="jump('building')">建筑</button>
          <button class="link" @click="jump('plat')">地图</button>
          <button class="link" @click="jump('#')">军事</button>
          <button class="link" @click="click()">测试</button>
        </div>
      </div>
    </div>
    <VFooter />
  </div>
</template>

<script>
  import VResourceBar from './sub/resource-bar'
  import VFooter from './sub/v-footer'
  import VBuildingList from './sub/building-list'
  import VProgress from './sub/progress'
  export default {
    name: 'manor',
    data () {
      return {
        activeType: 'farm',
        actionNumber: 23,
      }
    },
    components: { VResourceBar, VFooter, VProgress, VBuildingList },
    methods: {
      toggle: function () {
        this.axios.post('login', this.loginForm).then((response) => {
          console.info(response.data)
          this.$store.commit('setUser', response.data.user)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          localStorage.setItem('resource', JSON.stringify(response.data.resource))
          localStorage.setItem('building', JSON.stringify(response.data.building))
          window.location = '/#/manor'
        }).catch((error) => {
          this.$swal({
            text: (error.response.data) ? error.response.data : '服务器出错',
            type: 'error',
          })
        })
      },
    },
    computed: {
      nickname () {
        return this.$store.state.nickname
      },
      kingdom () {
        return this.$store.state.kingdom
      },
      capitalX () {
        return this.$store.state.capitalX
      },
      capitalY () {
        return this.$store.state.capitalY
      },
      resource () {
        return this.$store.state.resource
      },
    },
    created: function () {
      // 确认登录状态（检查心跳包）
      let isLogin = localStorage.getItem('isLogin')
      if (!isLogin) {
        window.location = '/#/'
      }

      let heartBeat = localStorage.getItem('heartBeat')
      if (!heartBeat || heartBeat < Math.ceil(new Date() / 1000) - 600) {
        this.axios.get('index').then((response) => {
          if (!response.data.isLogin) {
            this.axios.get('logout')
            localStorage.setItem('isLogin', 'false')
            window.location = '/#/'
          }
        }).catch((error) => {
          this.$swal({
            text: (error.response && error.response.data) ? error.response : '检查登录状态失败',
            type: 'error',
          })
        })
        localStorage.setItem('heartBeat', Math.ceil(new Date() / 1000).toString())
      }
      // 赋值用户数据
      let user = JSON.parse(localStorage.getItem('user'))
      this.$store.commit('setUser', user)
    },
  }
</script>

<style scoped lang="stylus">
  .manor-wrapper
    width: 100%
    min-height: 87.88vh
    .manor-header
      padding: 2rem 3rem
      text-align: left
      /*display: flex
      align-items: center
      justify-content: space-between*/
      .logo-wrapper
        display: inline-block
        width: 40%
        height: 60px
        line-height: 60px
        text-align: left
        vertical-align: middle
        .logo
          width: 300px
          height: 60px
      .manor-name
        margin 0 auto
        width: 40%
        height: 60px
        line-height: 60px
        font-size: 1.8rem
        text-align: center
        vertical-align: middle
    .manor-main-wrapper
      width: 100%
      .info-wrapper
        margin: 0 auto
        padding: 2rem
        width: 440px
        /*height: 360px*/
        -webkit-box-shadow:  0 2px 8px #cccccc
        -moz-box-shadow: 0 2px 8px #cccccc
        box-shadow: 0 2px 8px #cccccc
        -webkit-border-radius: 4px
        -moz-border-radius: 4px
        border-radius: 4px
        .row
          height: 3rem
          line-height: 3rem
          text-align: left
          font-size: 1.2rem
      .links-wrapper
        width: 100%
        text-align: center
        .link
          margin: 3rem 2rem
          width: 4rem
          height: 2.4rem
          line-height: 2.4rem
          text-align: center
</style>
