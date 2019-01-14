<template>
    <tr>
        <td>{{building.name}}</td>
        <td>{{building.number}}</td>
        <td>{{building.time}}</td>
        <td>{{building.level}}</td>
        <td><span v-for="(value, key) in building.occupy" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td><span v-for="(value, key) in building.material" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td><span v-for="(value, key) in building.product" :key="key">{{trans[key]}}: {{value}}</span></td>
        <td class="make-building">
            <input class="button orange-button" type="button" name="build" value="建造" @click="build">
            <input class="button green-button" type="button" name="destroy" value="拆除" @click="destroy">
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

<style scoped lang="scss">
  @import "../../assets/scss/global_css";
  table, th, td {
    border: 1px solid black;
    padding: 2px;
    border-collapse: collapse;
  }
  .make-building {
    border: none;
  }
  .button {
    margin: 0 1px 3px auto;
    height: 1.5rem;
    width: 2.7rem;
    font-size: 15px;
    line-height: normal;
    box-shadow: 0 2px #c3c3c3;
    :active {
      box-shadow: 0 1px #909090;
      transform: translateY(1px);
    }
  }
</style>
