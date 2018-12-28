<template>
  <div class="progress">
    <span class="progress-name"><small>{{process.name}}</small> - </span>
    <span v-show="!process.percent">空闲</span>
    <div v-show="process.percent" class="allbar">
      <div :style="{ width: progressBar + 'px' }" class="bar"></div>
    </div>
    <div class="bar-value" v-show="process.percent">{{Math.ceil(progressBar)}}%</div>
  </div>
</template>

<script>
  export default {
    name: 'VProgress',
    data () {
      return {
        // 该值用于显示建筑施工进程条。
        progressBar: 0
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
      // handleTest () {
      //   let test = this.test
      //   setInterval(function () {
      //     test += 20
      //     console.log(test + '  limian')
      //     return test
      //   }, 1000)
      //   console.log(test)
      // },
      save () {
        // 往父组件传递 del 方法，参数为当前id
        this.$emit('saveProgress', this.process.id)
      },
    },
    created: function () {
      let self = this
      update()
      setInterval(update, 1000)
      function update () {
        let nowTime = Math.ceil(new Date() / 1000)
        if (self.process.action) {
          if (self.process.endTime > nowTime) {
            let allTime = self.process.endTime - self.process.startTime // 整个进程的时间
            let remainTime = self.process.endTime - nowTime // 进程剩余的时间
            self.process.percent = 1 - remainTime / allTime
            // console.log('总时长' + allTime)
            // console.log('现在' + nowTime)
            // console.log('开始时间' + self.process.startTime)
            // console.log('结束时间' + self.process.endTime)
            // console.log('剩余时间' + remainTime)
            if (self.process.percent > 0) {
              self.progressBar = self.process.percent * 100 // 显示进程条，确保进程度值大于零。
            } else {
              self.progressBar = 0
            }
          } else {
            self.save()
            self.process.action = 0
            self.process.percent = 0
            self.progressBar = 0 // 清零进程条
          }
        }
      }
    }
  }
</script>

<style scoped>
  .progress {
    position: relative;
    left: 0;
    padding: 10px 0;
    min-width: 150px;
    height: 20px;
  }
  .success {
    height: 20px;
    background: #f30;
  }
  .allbar {
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
</style>
