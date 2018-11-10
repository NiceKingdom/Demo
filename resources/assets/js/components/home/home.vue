<template>
    <div>
        <el-row :gutter="20">
            <el-col :span="5">
                <h4>资源产出</h4>
                <ol style="padding-left: 23px;">
                    <li>人口产出：未知</li>
                    <li>食物：{{resourceOutput.food}} 斤 / 布鲁诺日</li>
                    <li>木材：{{resourceOutput.wood}} 斤 / 布鲁诺日</li>
                    <li>石块：{{resourceOutput.stone}} 斤 / 布鲁诺日</li>
                    <li>钱财：{{resourceOutput.money}} 元 / 布鲁诺日</li>
                </ol>
            </el-col>
            <el-col :span="19"></el-col>
        </el-row>
        <div class="flex-row resource-bar">
            <ResourceBay />
        </div>
    </div>
</template>

<script>
  import Swal from 'sweetalert2'
  import { mapState } from 'vuex'
  import ResourceBay from '../sub-resourceBar/resourceBar'

  export default {
    name: "v-home",
    data() {
      return {
        params: params
      }
    },
    components: {
      ResourceBay
    },
    methods: {
      // resourceUpdate() {
      //   this.$store.commit('increment')
      // },
    },
    computed: mapState({
      resourceOutput: state => state.resourceOutput,
    })
  }
</script>

<style type="text/scss" scoped>
    p {
        font-size: 0.85rem;
        color: #404040;
    }

    .description {
        text-indent: 2rem;
    }

    .text-center {
        text-align: center;
    }
</style>
