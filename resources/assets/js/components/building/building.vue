<template>
    <div>
        <div class="flex-row main">
            <div class="occupy5"></div>
            <div class="occupy10">
                <buildingList :building="building"/>
            </div>
        </div>
        <div class="flex-row resource-bar">
            <ResourceBay :resource="resource"/>
        </div>
    </div>
</template>

<script>
  import Swal from 'sweetalert2'
  import ResourceBay from '../sub-resourceBar/resourceBar'
  import buildingList from '../sub-buildingList/buildingList'

  export default {
    name: "Building",
    data() {
      return {
        building: {},
        resource: {},
      }
    },
    components: {
      ResourceBay,
      buildingList
    },
    props: {
      userData: {
        type: Object
      }
    },
    methods: {
      test() {
        // console.info(new Date().getTime()/1000)
      },
      getBuilding() {
        let buildingList = JSON.parse(localStorage.getItem('buildingList'))
        if (buildingList === null) buildingList = { version: 0 }

        this.$http.get(`/building/list/${buildingList.version}`).then(response => {
          response = response.body
          localStorage.setItem('buildingList', JSON.stringify(response))
          delete response.version
          this.building = response
        }, response => {
          let info = (response.status !== 403) ? '服务器错误，进群了解一下吧' : response.bodyText;
          Swal({
            type: 'error',
            text: info
          })
          delete buildingList.version
          this.building = buildingList
        })
      },
    },
    created() {
      Object.assign(this.$data, this.userData)
      this.getBuilding()
      // console.info(this.$data)
    },
  }
</script>

<style scoped>

</style>
