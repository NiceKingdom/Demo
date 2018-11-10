<template>
    <div>
        <VResourceBar />
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
    </div>
</template>

<script>
  import VResourceBar from './sub/resource-bar'
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
    components: { VResourceBar, VProgress, VBuildingList },
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
      // 假装服务器状态是已登录
      let isLogin = true
      if (!isLogin || !localStorage.getItem('user')) {
        window.location = '/#/'
      }
      let user = JSON.parse(localStorage.getItem('user'))
      this.$store.commit('setUser', user)

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

      var save = function () {
        let keepLive = Math.ceil(new Date() / 1000) - localStorage.getItem('keepLive')
        if (keepLive > 15) {
          this.$store.dispatch('save', resource, keepLive)
        }
        setTimeout(save, 15000)
      }
      setTimeout(save, 15000)
    },
  }
</script>

<style scoped>
    table, th, td {
        border: 1px solid black;
        padding: 2px;
        border-collapse: collapse;
    }
    .flex {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        padding-bottom: 3px;
    }
    .heading {
        margin-top: 4px;
        margin-bottom: 8px;
    }

    .manor {
        background-color: #d0f3d3;
        padding: 17px;
    }
</style>
