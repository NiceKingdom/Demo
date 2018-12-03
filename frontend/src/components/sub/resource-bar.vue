<template>
    <div class="flex-block">
        <div class="flex-row">
          <span>{{resource}}</span>
            <!--<span class="flex-item" v-for="item in resource" :key="item.name">-->
                <!--<b style="color: #000;">{{item.name}}</b>：-->
                <!--<span style="color: #636363;">{{item.value}}</span>-->
                <!--(<span class="b">{{item.output}}</span>)-->
            <!--</span>-->
        </div>
    </div>
</template>

<script>
export default {
  name: 'ResourceBar',
  computed: {
    resource () {
      return this.$store.state.resource
    },
    res () {
      return new Date()
    },
  },
  methods: {
    test: function () {
      return true
    },
  },
  mounted () {
    setInterval(this.test, 1000)
  }
}
</script>

<style scoped>
    .flex-row {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
    }

    .flex-item {
        justify-content: center;
        min-width: 80px;
        padding: 4px 8px;
        margin: 4px 8px;
        background: rgba(212, 198, 120, 0.12);
    }
</style>
