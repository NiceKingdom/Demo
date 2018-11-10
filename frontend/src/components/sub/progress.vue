<template>
    <div style="padding-bottom: 10px;">
        <span><small>{{process.name}}</small> - </span>
        <span v-if="!process.percent">空闲</span>
        <progress v-else :value="process.percent" max="1">123</progress>
    </div>
</template>

<script>
  export default {
    name: 'VProgress',
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
          } else {
            self.save()
            self.process.action = 0
            self.process.percent = 0
          }
        }
      }
    }
  }
</script>

<style scoped>
    .progress {
        width: 200px;
        height: 20px;
        background: #ddd;
    }
    .success {
        height: 20px;
        background: #f30;
    }
</style>
