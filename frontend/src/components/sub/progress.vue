<template>
  <div class="progress">
    <span class="progress-name"><small>名称：{{process.name}}</small></span>
    <span class="status-before" v-show="!process.percent">状态：空闲</span>
    <span class="status-ing" v-show="process.percent">施工类型：{{this.action}}</span>
    <br/>
    <span class="status-ing" v-show="process.percent">施工进度-</span>
    <div v-show="process.percent" class="allBar">
      <div :style="{ width: progressBar + 'px' }" class="bar"></div>
    </div>
    <div class="bar-value" v-show="process.percent">{{Math.ceil(progressBar)}}%</div>
    <button class="cancel-btn white-button" v-show="process.percent && process.action === 1" @click="cancel">取消</button>
  </div>
</template>

<script>
  export default {
    name: 'VProgress',
    data () {
      return {
        // 该值用于显示建筑施工进程条。
        progressBar: 0,
        action: '',
        buildProgress: Object
      }
    },
    props: {
      process: {
        type: Object,
        require: true
      },
    },
    computed: {
    },
    methods: {
      save () {
        // 往父组件传递 del 方法，参数为当前id
        this.$emit('saveProgress', this.process.id)
      },
      cancel: function () {
          this.axios.get('building/recall/' + this.process.id).then((response) => {
            let type = 'success'
            if (response.data[0] === 'failed') {
              type = 'info'
            } else {
              this.process.action = 0
              this.process.percent = 0
              this.progressBar = 0 // 清零进程条
            }
            process.percent = undefined
            this.$swal({
              text: response.data[1],
              type: type
            })
          }).catch((error) => {
            this.$swal({
              text: (error.response.data) ? error.response.data : '服务器出错',
              type: 'error',
            })
          })
      }
    },
    created: function () {
      let self = this
      function update () {
        let nowTime = self.nowTime()
        if (self.process.action) {
          if (self.process.endTime > nowTime) {
            let allTime = self.process.endTime - self.process.startTime // 整个进程的时间
            let remainTime = self.process.endTime - nowTime // 进程剩余的时间
            self.process.percent = 1 - remainTime / allTime
            if (self.process.percent > 0) {
              self.progressBar = self.process.percent * 100 // 显示进程条，确保进程度值大于零。
            } else {
              self.progressBar = 0
            }
          } else {
            // 清空进程条
            self.save()
            self.process.action = 0
            self.process.percent = 0
            self.progressBar = 0
          }
        }
      }

      setInterval(update, 1000)
    },
    beforeUpdate: function () {
      // 标记施工类型
      if (this.process.action === 1) {
        this.action = '建造'
      } else if (this.process.action === 2) {
        this.action = '拆除'
      } else {
        console.log(this.process.action)
      }
    }
  }
</script>

<style scoped lang="scss">
  @import "../../assets/scss/global_css";
  .progress {
    position: relative;
    left: 0;
    margin: 1vh 0;
    padding: 1.2vh 2vw;
    border: 1px solid #cccccc;
  }
  .progress-name {
    display: block;
    text-align: center;
    font-size: 1.2rem;
    font-weight: bold;
  }
  .status-before {
    font-size: 14px;
    text-align: center;
  }
  .status-ing {
    font-size: 14px;
    text-align: left;
  }
  .allBar {
    display: inline-block;
    width: 100px;
    height: 10px;
    background: #dadada;
    border-radius: 10px;
  }
  .bar {
    float: left;
    display: inline-block;
    height: 10px;
    border-radius: 10px;
    background: linear-gradient(to bottom right , skyblue,steelblue);
  }
  .bar-value {
    display: inline-block;
    font-size: 12px;
    color: #a8a9a6;
  }
  .cancel-btn {
    height: 1.5rem;
    width: 2.5rem;
    font-size: 12px;
    line-height: normal;
    color: #555555;
    border: 2px solid #dddeda;
  }
</style>
