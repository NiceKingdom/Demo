<template>
  <div class="flex-block">
    <div class="flex-row">
            <span class="flex-item" v-for="item in resource" :key="item.name">
                <b style="color: #000;">{{item.name}}</b>：
                <span style="color: #636363;">{{item.value}}</span>
                (<span title="每小时" class="b">{{item.output}}</span>)
            </span>
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
    },
  }
</script>

<style scoped>
  .flex-block {
    width: 100%;
    border: 1px solid rgba(0, 0, 0, 0.1);
    background: #e6e9c0;
  }
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
  }
</style>
