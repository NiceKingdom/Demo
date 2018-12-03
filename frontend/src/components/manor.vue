<template>
<<<<<<< HEAD
  <div class="manor-wrapper">
    <div class="manor-header">
      <div class="logo-wrapper">
        <img class="logo" src="../assets/logo.png" alt="">
      </div>
      <div class="manor-name" @click="jump('manor')">{{kingdom}}</div>
=======
    <div>
      <div>{{actionNumber}}</div>
      <div>{{resource.people.value}}</div>
        <!--<VResourceBar />-->
        <div class="flex">
            <div class="manor">
                <h3 class="heading">领地</h3>
                <div>
                    <span>王国：{{kingdom}}；</span>
                    <span>领主：{{nickname}}；</span>
                    <span>坐标：X{{capitalX}}, Y{{capitalY}}</span>
                </div>
            </div>
        </div>
>>>>>>> aa5586e7fd5386ad7657ad42adeeb9c7dbfff1f6
    </div>
    <div class="manor-main-wrapper">
      <div class="info-wrapper">
        <div class="row">王国：{{kingdom}}</div>
        <div class="row">领主：{{nickname}}</div>
        <div class="row">坐标：({{capitalX}}, {{capitalY}})</div>
      </div>
      <div class="links-wrapper">
        <button class="link" @click="jump('building')">建筑</button>
        <button class="link" @click="jump('#')">地图</button>
        <button class="link" @click="jump('#')">军事</button>
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
      test: function () {
        this.actionNumber++
        this.$store.commit('secUpdate')
        console.info(this.resource.people.value)
        // this.resource.people.value++
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
<<<<<<< HEAD
=======
      people () {
        return this.$store.state.resource.people.value
      },
      // 建筑进程
      schedules () {
        return this.$store.state.schedules
      },
      // 建筑清单
      buildingList () {
        return this.$store.state.buildingList
      },
      // 建筑清单的类型
      buildType: function () {
        let result = []
        for (let key in this.buildingList) {
          result.push(key)
        }
        return result
      },
>>>>>>> aa5586e7fd5386ad7657ad42adeeb9c7dbfff1f6
    },
    created: function () {
      // 确认登录状态（检查心跳包）
      let heartBeat = localStorage.getItem('heartBeat')
      if (!heartBeat || heartBeat < Math.ceil(new Date() / 1000) - 600) {
        this.axios.get('index').then((response) => {
          if (!response.data.isLogin) {
            this.axios.get('logout')
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
<<<<<<< HEAD
=======

      // 假装成功请求建筑清单
      let buildingList = {
        list: {
          farm: [
            {
              name: '一级农田',
              level: 1,
              time: 75,
              product: {
                food: 1
              },
              material: {
                wood: 10
              },
              occupy: {
                people: 1
              }
            },
            {
              name: '二级农田',
              level: 2,
              time: 105,
              product: {
                food: 1.2
              },
              material: {
                wood: 13
              },
              occupy: {
                people: 1
              }
            }
          ],
          sawmill: [
            {
              name: '一级伐木营地',
              level: 1,
              time: 120,
              product: {
                wood: 0.6
              },
              material: {
                money: 10
              },
              occupy: {
                people: 1
              }
            },
            {
              name: '二级伐木营地',
              level: 2,
              time: 165,
              product: {
                wood: 1.6
              },
              material: {
                money: 28
              },
              occupy: {
                people: 2
              }
            }
          ]
        },
        building: {
          id: 26,
          userId: 26,
          farm01: 13,
          farm02: 0,
          sawmill01: 0,
          sawmill02: 0,
          created_at: '2018-09-16 10:40:52',
          updated_at: '2018-09-16 15:04:54'
        }
      }
      this.$store.commit('setBuildingList', buildingList)

      // 假装成功请求领地资源
      let resource = {
        people: 200,
        peopleChip: 0.00,
        peopleOutput: 0.00,
        food: 3000,
        foodChip: 0.0000,
        foodOutput: 0,
        wood: 2000,
        woodChip: 0.0000,
        woodOutput: 0,
        stone: 1000,
        stoneChip: 0.0000,
        stoneOutput: 0,
        money: 3500,
        moneyChip: 0.0000,
        moneyOutput: 0,
      }
      this.$store.commit('setResource', resource)

      setInterval(this.test, 1000)
>>>>>>> aa5586e7fd5386ad7657ad42adeeb9c7dbfff1f6
    },
  }
</script>

<style scoped lang="stylus">
  .manor-wrapper
    width: 100%
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
        display: inline-block
        width: 40%
        height: 60px
        line-height: 60px
        font-size: 1.8rem
        text-align: left
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
