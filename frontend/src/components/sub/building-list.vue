<template>
    <tr>
        <td>{{building.name}}</td>
        <td>{{building.number}}</td>
        <td>{{building.time}}</td>
        <td>{{building.level}}</td>
        <td><span v-for="(value, key) in building.occupy" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td><span v-for="(value, key) in building.material" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td><span v-for="(value, key) in building.product" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td>
            <input type="button" name="build" value="建造" @click="build">
            <input type="button" name="destroy" value="拆除" @click="destroy">
        </td>
    </tr>
</template>

<script>
  export default {
    name: 'BuildingList',
    props: {
      building: {
        type: Object,
        require: true
      },
    },
    methods: {
      build: function () {
        this.$emit('build', {
          level: this.building.level,
        })
      },
      destroy: function () {
        this.$emit('destroy', {
          level: this.building.level,
        })
      },
    },
    computed: {
      trans () {
        return this.$store.state.resourceTrans
      },
    }
  }
</script>

<style scoped>
    table, th, td {
        border: 1px solid black;
        padding: 2px;
        border-collapse: collapse;
    }
</style>
