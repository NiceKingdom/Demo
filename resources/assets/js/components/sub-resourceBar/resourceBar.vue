<template>
    <div class="flex-row">
        <div class="flex-item" v-for="(value, key) in resource">{{ resourceName[key] }}：<span>{{ value }}</span></div>
    </div>
</template>

<script>
  export default {
    name: "ResourceBar",
    computed: {
      resourceName () {
        return this.$store.state.resourceName
      },
      resource () {
        return this.$store.state.resource
      },
    },
  }
</script>

<style scoped>
    .flex-row {
        display: flex;
        background: rgba(212, 198, 120, 0.12);
    }

    .flex-item {
        justify-content: center;
        min-width: 80px;
        margin: 4px 4px;
    }
</style>
