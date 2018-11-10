@extends('layouts.app')

@section('title', '王国')
@section('content')
  @verbatim
    <el-row type="flex" justify="center" style="margin-bottom: 20px;">
      <el-col :offset="4" :span="16">
        <h3>施工队</h3>
        <div style="text-align:center;">
          <el-col :span="4" v-for="schedule in schedulesShow">
            <h4>{{schedule.name}}</h4>
            <el-progress :show-text="false" :stroke-width="16" :percentage="schedule.percent" :status="schedule.status"></el-progress>
          </el-col>
        </div>
      </el-col>
    </el-row>

    <el-row type="flex" justify="center">
      <el-col :offset="3" :span="16">
        <span style="padding-left: 3px;" v-for="type in buildType" @click="toggle(type)">
          <el-tag>{{typeTrans(type)}}</el-tag>
        </span>
        <el-input-number size="small" v-model="number" style="float:right; margin-right: 40px;"></el-input-number>

        <el-table :data="buildList" style="width: 100%">
          <el-table-column prop="name" label="名称" width="120">
          </el-table-column>
          <el-table-column prop="level" label="级别" width="80">
          </el-table-column>
          <el-table-column prop="materialDesc" label="耗材">
          </el-table-column>
          <el-table-column prop="productDesc" label="产出">
          </el-table-column>
          <el-table-column prop="occupyDesc" label="常驻">
          </el-table-column>
          <el-table-column prop="time" label="耗时(秒)" width="100">
          </el-table-column>
          <el-table-column prop="number" label="拥有" width="100">
          </el-table-column>
          <el-table-column fixed="right" label="操作" width="100">
            <template slot-scope="scope">
              <el-button @click="build(scope.row)" type="text" size="small">建筑</el-button>
              <el-button @click="destroy(scope.row)" type="text" size="small">拆除</el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>
  @endverbatim
@endsection

@section('js')
  <script>
    let data = JSON.parse(localStorage.getItem('user'))
    data.buildingList = JSON.parse(localStorage.getItem('building'))
    data.activeHeader = 'building'
    data.activeType = 'farm'
    data.number = 1
    data.schedules = [
      {startTime: 0, endTime: 0, name: "建筑队", status: "success"},
      {startTime: 0, endTime: 0, name: "建筑队", status: "exception"},
    ]

    let methods = {
      typeTrans: function (type) {
        typeTrans = {
          farm: '农场',
          sawmill: '伐木',
        }
        return typeTrans[type]
      },
      build(row) {
        this.$set(this.buildList[0], 'number', 231)
        console.info(this.buildList[0])
        if (!this.check()) {
          swal({
            text: '没有空闲的建筑队',
            type: 'error',
          })
        }
        let data = {
          type: this.activeType,
          level: row.level,
          number: this.number,
        }
        // this.post('building/build', data, 'sayResponse')
      },
      // 检查是否存在休闲
      check() {
        for (let key in this.schedules) {
          if (this.schedules[key].status === 'success') {
            return key;
          }
        }
      },
      // 切换类型
      toggle(type) {
        this.activeType = type
      },necessary
      handleCreated() {
        this.get('building/schedule', '', function (request) {
          app.$data.schedules = request.schedules;
          app.$data.building = request.building;
        })
      },
    }

    let computed = {
      schedulesShow: function () {
        let result = []
        for (let key in this.schedules) {
          let allTime = this.schedules[key].endTime - this.schedules[key].startTime
          let nowTime =+ new Date() / 1000
          this.schedules[key].percent = (allTime - Math.ceil(remainingTime)) / allTime

          if (this.schedules[key].percent <= 0) {
            this.schedules[key].percent = 100
            this.schedules[key].status = 'success'
          }

          result.push(this.schedules[key])
        }
        return result
      },
      buildType: function () {
        let result = []
        for (let key in this.buildingList) {
          result.push(key)
        }
        return result
      },
    }
  </script>
@endsection
