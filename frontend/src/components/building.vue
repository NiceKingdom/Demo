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
                  <span class="button farm-wood"
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
            <button class="link" @click="population">政策</button>
            <button class="link" @click="jump('building')">建筑</button>
            <button class="link" @click="jump('plat')">地图</button>
            <button class="link" @click="jump('#')">军事</button>
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
    name: 'building',
    data () {
      return {
        activeType: 'farm',
        actionNumber: 1,
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
        if (!data) {
          this.$swal({
            text: '程序的生命中，缺少了永恒般的基因（这却不是你的错误）。',
            type: 'error',
          })
          return false
        }
        data.type = this.activeType
        data.number = this.actionNumber
        // 发送建筑请求
        this.axios.post('building/build', data).then((response) => {
          let type = 'success'
          if (response.data[0] === 'failed') {
            type = 'error'
          }
          this.$swal({
            text: response.data[1],
            type: type,
          })
        }).catch((error) => {
          this.$swal({
            text: (error.response.data) ? error.response.data : '服务器出错',
            type: 'error',
          })
        })

        // 请求当前领地的资源
        this.axios.get('user/get-resource').then((response) => {
          if (response.data[0] === 'succeed') {
            localStorage.setItem('resource', JSON.stringify(response.data))
            this.$store.commit('setResource', response.data)

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

            this.axios.get('building/schedule').then((response) => {
              this.$store.commit('setSchedules', response.data)
            }).catch((error) => {
              this.$swal({
                text: (error.response.data) ? error.response.data : '服务器出错',
                type: 'error',
              })
            })
          } else {
            this.$swal({
              text: response.data[1],
              type: 'error',
            })
          }
        }).catch((error) => {
          this.$swal({
            text: (error.response.data) ? error.response.data : '服务器出错',
            type: 'error',
          })
        })
      },
      destroy: function (data) {
        if (this.actionNumber < 1) {
          this.$swal({
            text: '奇迹般的拆除数量，您确认没有错吗？',
            type: 'error',
          })
          return false
        }
        if (!data) {
          this.$swal({
            text: '程序的生命中，缺少了永恒般的基因（这却不是你的错误）。',
            type: 'error',
          })
          return false
        }
        data.type = this.activeType
        data.number = this.actionNumber
        // 发送拆除请求
        this.axios.post('building/destroy', data).then((response) => {
          if (response.data[0] === 'succeed') {
            this.$swal({
              text: response.data[1],
              type: 'success',
            })
            // 获取用户数据并赋值
            this.axios.get('building/index').then((response) => {
              localStorage.setItem('building', JSON.stringify(response.data.building))
              localStorage.setItem('buildingList', JSON.stringify(response.data.list))
              this.$store.commit('setBuildingList', {
                'building': response.data.building,
                'list': response.data.list,
              })
              this.$store.commit('setResource', response.data.resource)
              this.$store.commit('setSchedules', response.data.schedule)
            }).catch((error) => {
              if (error.response.data.message === 'Unauthenticated.') {
                localStorage.clear()
                window.location.reload()
              }
              this.$swal({
                text: (error.response.data) ? error.response.data : '服务器出错',
                type: 'error',
              })
            })
          } else {
            this.$swal({
              text: response.data[1],
              type: 'error',
            })
          }
        }).catch((error) => {
          this.$swal({
            text: (error.response.data) ? error.response.data : '服务器出错',
            type: 'error',
          })
        })
      },
      typeTrans: function (type) {
        let typeTrans = {
          farm: '农场',
          sawmill: '伐木',
        }
        return typeTrans[type]
      },

      // 【政策弹窗】
      population: function () {
        const populationSwal = this.$swal.mixin({
          confirmButtonClass: 'sBtn',
          cancelButtonClass: 'sBtn',
          buttonsStyling: true,
          footer: '<button @click="test" class="sBtn">近期政令历史</button>',
          onBeforeOpen: () => {
            // 设置footer的样式
            let cancelBtn = document.getElementsByClassName('sBtn')[1]
            cancelBtn.setAttribute('style', 'background:#3085d6')
            // 设置cancelButton的样式
            let pastBtn = document.getElementsByClassName('sBtn')[2]
            pastBtn.setAttribute('style', 'background:white;border:none;color:#3085d6;font-size:16px')

            // 为footer绑定点击事件
            pastBtn.addEventListener('click', () => {
              this.axios.post('lord/policy/history',
                {
                  page: 2,
                  size: 6,
                  x: this.capitalX,
                  y: this.capitalY
                }).then((response) => {
                  let history = ''
                  for (let i = 0; i < response.data.length; i++) {
                    history += response.data[i].created_at + response.data[i].info + '<hr />'
                  }
                populationSwal({
                  title: '近期政令历史',
                  html: history,
                  showConfirmButton: false,
                })
                }
              ).then((result) => {
                console.info(result)
              }).catch((error) => {
                console.info(error)
                this.$swal({
                  text: (error.response.data) ? error.response.data : '服务器出错',
                  type: 'error',
                })
              })
            })
          }
        })

        // 动作完成后弹出提示窗口时调用的函数，interval为间隔时间，type为操作的类型：居民驱逐 || 流民招募。
        function completeFunction (interval, type) {
            setTimeout(function () {
              completePop({
              title: type + '已结束',
              text: '可在“近期政令历史”中查看结果',
              type: 'success',
              showCancelButton: false
            })
          }, interval)
        }
        // 动作完成后的提示窗口
        const completePop = this.$swal.mixin({
          buttonsStyling: true,
        })

        // 流民招募 & 居民驱逐
        populationSwal({
          title: '政策与指令',
          showCancelButton: true,
          confirmButtonText: '流民招募启事',
          cancelButtonText: '居民驱逐通告',
          footer: '<button @click="test" class="sBtn">近期政令历史</button>',
        }).then((result) => {
          // 流民招募
          if (result.value) {
            this.axios.post('lord/policy/enlisting/open', {
              x: this.capitalX,
              y: this.capitalY
            }).then((response) => {
              console.info(response.data)
              this.$swal({
                title: '招募启事已发布',
                text: '请稍后到“近期政令历史”中查看结果',
                type: 'success'
              })

              // 招募结束提示窗口
              let nowTime = this.nowTime()
              let interval = (response.data - nowTime) * 1000
              let type = '流民招募'
              // 设定完成的提示窗口，传参数interval和type。
              completeFunction(interval, type)
            }).catch((error) => {
              this.$swal({
                text: (error.response.data) ? error.response.data : '服务器出错',
                type: 'error',
              })
            })
            // 居民驱逐
          } else if (
            result.dismiss === this.$swal.DismissReason.cancel
          ) {
            populationSwal({
              title: '驱逐居民数',
              type: 'info',
              html: '<div>预计驱逐  ' +
                '<input type="number" min="1" onkeyup="this.value=this.value.replace(/\\D/g,\'\')" class="leave"/>' +
                '  人</div>',
              showCancelButton: true,
              showCloseButton: true,
              confirmButtonText: '支付金钱',
              cancelButtonText: '支付粮食',
            }).then((resultNext) => {
              // 若点击确定，则发送驱逐请求
              let inputValue = document.getElementsByClassName('leave')[0].value
              if (resultNext.value) {
                this.axios.post('lord/policy/deported/open', {
                  x: this.capitalX,
                  y: this.capitalY,
                  costType: 1,
                  number: inputValue
                }).then((response) => {
                  this.$swal({
                    title: '驱逐通告已发布',
                    text: '请稍后到“近期政令历史”中查看结果',
                    type: 'success'
                  })

                  // 驱逐结束提示窗口
                  let nowTime = this.nowTime()
                  let interval = (response.data - nowTime) * 1000
                  let type = '居民驱逐'
                  // 设定完成的提示窗口，传参数interval和type。
                  completeFunction(interval, type)
                }).catch((error) => {
                    console.info(error)
                    this.$swal({
                      text: (error.response.data) ? error.response.data : '服务器出错',
                      type: 'error',
                    })
                  })
              } else if (
                resultNext.dismiss === this.$swal.DismissReason.cancel
              ) {
                this.axios.post('lord/policy/deported/open', {
                  x: this.capitalX,
                  y: this.capitalY,
                  costType: 2,
                  number: inputValue
                }).then((response) => {
                  console.info(response)
                  this.$swal({
                    title: '驱逐通告已发布',
                    text: '请稍后到“近期政令历史”中查看结果',
                    type: 'success'
                  })

                  // 驱逐结束提示窗口
                  let nowTime = this.nowTime()
                  let interval = (response.data - nowTime) * 1000
                  let type = '居民驱逐'
                  // 设定完成的提示窗口，传参数interval和type。
                  completeFunction(interval, type)
                }).catch((error) => {
                  console.info(error)
                  this.$swal({
                    text: (error.response.data) ? error.response.data : '服务器出错',
                    type: 'error',
                  })
                })
              }
            })
          }
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
      // 检查用户登录状态
      if (!this.checkLogin()) {
        window.location = '/#/'
      }

      // 赋值用户数据
      let user = JSON.parse(localStorage.getItem('user'))
      this.$store.commit('setUser', user)

      // 获取用户数据并赋值
      this.axios.get('building/index').then((response) => {
        localStorage.setItem('building', JSON.stringify(response.data.building))
        localStorage.setItem('buildingList', JSON.stringify(response.data.list))
        this.$store.commit('setBuildingList', {
          'building': response.data.building,
          'list': response.data.list,
        })
        this.$store.commit('setResource', response.data.resource)
        this.$store.commit('setSchedules', response.data.schedule)
      }).catch((error) => {
        if (error.response.data.message === 'Unauthenticated.') {
          localStorage.clear()
          window.location.reload()
        }
        this.$swal({
          text: (error.response.data) ? error.response.data : '服务器出错',
          type: 'error',
        })
      })
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
        display: flex
        justify-content: center
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
          margin: 2rem 2rem
</style>

<style scoped lang="scss">
  @import "../assets/scss/global_css";
  .farm-wood {
    height: 30px;
    width: 50px;
    margin: 0 5px;
    align-content: center;
    font-size: 15px;
    background: #6d7973;
  }
  #number {
    height: 20px;
    border: 1px solid #c3c3c3;
    border-radius: 10px;
    padding-left: 6px;
    color: #555555;
    outline: none;
  }
</style>

<style>
  .leave {
    border: 1px solid lightblue;
    border-radius: 10px;
    min-width: 20px;
    min-height: 25px;
    font-size: 18px;
    color: #3085d6;
    padding-left: 10px;
  }
</style>
