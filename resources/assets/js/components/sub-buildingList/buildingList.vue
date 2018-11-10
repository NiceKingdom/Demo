<template>
    <div class="flex-row">
        <div v-for="(value, key) in building">
            <h4>{{ buildingName[key] }}</h4>

            <table>
                <thead>
                <tr>
                    <th width="150">名称</th>
                    <th width="200">成本</th>
                    <th width="200">产出</th>
                    <th width="200">操作</th>
                </tr>
                </thead>

                <tbody v-for="subValue in value">
                <tr>
                    <td>{{ subValue.name }}</td>
                    <td><span v-for="(v, k) in subValue.material">{{ resourceName[k] }}：{{ v }}</span></td>
                    <td><span v-for="(v, k) in subValue.product">{{ resourceName[k] }}：{{ v }}</span></td>
                    <td>
                        <button type="button" class="button-blue button-small" @click="build(subValue.level)">建造</button>
                        <button type="button" class="button-red button-small" @click="destroy(subValue.level)">拆除</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <br>
        </div>

        <!--<div class="flex-item" v-for="(value, key) in resource">{{ resourceName[key] }}：<span>{{ value }}</span></div>-->
    </div>
</template>

<script>
  export default {
    name: "buildingList",
    data() {
      return {
        buildingName: {
          'farm': '粮食',
          'logging': '木材',
        },
        resourceName: {
          'people': '人口',
          'food': '食物',
          'wood': '木材',
          'money': '钱财',
          'stone': '石块',
        }
      }
    },
    props: {
      building: {
        type: Object
      }
    },
    methods: {
      build: function () {
        // todo: build building.
      },
      destroy: function () {
        // todo: destroy building.
      }
    }
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

    table {
        border-collapse:collapse;
    }

    tr:hover {
        background-color: rgba(182, 182, 182, 0.52);
    }

    table, th, td {
        border: 1px solid black;
    }

    th, td {
        height: 2rem;
        text-align: center;
    }
</style>