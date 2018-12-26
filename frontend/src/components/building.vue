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
              <label for="number">操作数量</label>
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
          <button class="link" @click="jump('#')">地图</button>
          <button class="link" @click="jump('#')">军事</button>
          <button class="link" @click="click()">测试</button>
        </div>
      </div>
    </div>
    <VFooter/>
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
      toggle: function (type) {
        this.activeType = type
      },
      build: function (data) {
        if (this.actionNumber < 1) {
          this.$swal({
            text: '奇迹般的建造数量，您确认没有错吗？',
            type: 'error',
          })
          return false
        }

        data.type = this.activeType
        data.number = this.actionNumber
        if (data) {
          // 假装成功发送建筑请求
          console.info(data)

          // 假装成功请求当前领地资源
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

          // 假装成功请求当前建筑清单
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
              farm02: 2,
              sawmill01: 0,
              sawmill02: 0,
              created_at: '2018-09-16 10:40:52',
              updated_at: '2018-09-16 15:04:54'
            }
          }
          this.$store.commit('setBuildingList', buildingList)
        }
      },
      destroy: function (data) {
        if (this.actionNumber < 1) {
          this.$swal({
            text: '奇迹般的拆除数量，您确认没有错吗？',
            type: 'error',
          })
          return false
        }
        data.type = this.activeType
        data.number = this.actionNumber
        // 假装成功发送拆除请求
        console.info(data)
      },
      saveProgress: function () {
        // TODO: 进程完成
        // （避免时间存在偏差带来的额外 HTTP 开销）本地修改数据即可
        // this.$store.commit('increment')
      },
      typeTrans: function (type) {
        let typeTrans = {
          farm: '农场',
          sawmill: '伐木',
        }
        return typeTrans[type]
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
    },
    created: function () {
      // 确认登录状态（检查心跳包）
      let isLogin = localStorage.getItem('isLogin')
      if (!isLogin || isLogin === 'false') {
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
          console.info(error.response)
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

      // 获取建筑清单并赋值
      this.axios.get('building/list').then((response) => {
        localStorage.setItem('building', JSON.stringify(response.data.building))
        localStorage.setItem('buildingList', JSON.stringify(response.data.list))
        this.$store.commit('setBuildingList', response.data)
      }).catch((error) => {
        this.$swal({
          text: (error.response.data) ? error.response.data : '服务器出错',
          type: 'error',
        })
      })

      // 假装成功请求已建筑列表
      let schedules = [
        {
          id: 76,
          userId: 26,
          name: '建筑队',
          startTime: 1537768864,
          endTime: 1537768939,
          type: 'farm',
          level: 1,
          action: 1,
          number: 1,
          created_at: '2018-09-16 10:40:52',
          updated_at: '2018-09-24 14:01:04'
        },
        {
          id: 77,
          userId: 26,
          name: '建筑队',
          startTime: 1537768865,
          endTime: 1537768940,
          type: 'farm',
          level: 1,
          action: 1,
          number: 1,
          created_at: '2018-09-16 10:40:52',
          updated_at: '2018-09-24 14:01:05'
        },
        {
          id: 78,
          userId: 26,
          name: '建筑队',
          startTime: 0,
          endTime: 0,
          type: '',
          level: 0,
          action: 0,
          number: 0,
          created_at: '2018-09-16 10:40:52',
          updated_at: '2018-09-16 10:40:52'
        }
      ]
      this.$store.commit('setSchedules', schedules)

      // 赋值领地资源
      this.$store.commit('setResource', JSON.parse(localStorage.getItem('resource')))
    },
  }
</script>

<style scoped lang="stylus">
  .building-wrapper
    min-height: 87.88vh
    width: 100%
    .building-header
      padding: 2rem 3rem
      text-align: left
      /*display: flex
      align-items: center
      justify-content: space-between*/
      .logo-wrapper
        display: inline-block
        width: 300px
        height: 60px
        line-height: 60px
        text-align: left
        vertical-align: middle
        .logo
          width: 100%
          height: auto
      .resource-wrapper
        display: inline-block
        margin: 0 1rem
        width: 40%
        vertical-align: middle
      .building-name
        display: inline-block
        margin-left: 2rem
        height: 60px
        line-height: 60px
        font-size: 2rem
        text-align: left
        vertical-align: middle
    .building-main-wrapper
      width: 100%
      .info-wrapper
        padding: 2rem
        .left
          padding: 5px
          display: inline-block
          margin-right: 1.2rem
          border: 1px solid #cccccc
        .right
          display: inline-block
          border: 1px solid #cccccc
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
