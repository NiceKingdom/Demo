import Vue from 'vue'
import VueX from 'vuex'

Vue.use(VueX)

export default new VueX.Store({
  state: {
    loginName: '',
    nickname: '',
    kingdom: '',
    capitalX: '',
    capitalY: '',
    resource: {
      people: 0,
      food: 0,
      wood: 0,
      stone: 0,
      money: 0,
      area: 0,
    },
    buildingList: {},
    schedules: {},
    resourceTrans: {
      people: '人口',
      food: '食物',
      wood: '木材',
      stone: '石材',
      money: '金钱',
      area: '面积',
    },
  },
  mutations: {
    /**
     * 资源自增
     * 资源数量随时间匀速增长，使用函数setUpdate和update
     * @param state
     */
    setUpdate (state) {
      let Kind = ['people', 'food', 'wood', 'stone', 'money', 'area']
      Kind.forEach(MathUpdate)
      function MathUpdate (item) {
        let resource = state.resource
        let SecOutput = resource[item].output / 3600
        resource[item].value += Math.floor(SecOutput)
        resource[item].oddment += SecOutput - Math.floor(SecOutput)
        if (resource.people.oddment > 1) {
          state.resource[item].value++
          state.resource[item].oddment--
        }
      }
    },

    /**
     * 初始化用户的基础信息
     * @param state
     * @param userData
     */
    setUser (state, userData) {
      state.loginName = userData.nickname
      state.nickname = userData.nickname
      state.kingdom = userData.kingdom
      let capital = userData.capital.split(',')
      state.capitalX = capital[0]
      state.capitalY = capital[1]
    },

    /**
     * 初始化用户的施工队等队列信息
     * @param state
     * @param shcedules
     */
    setSchedules (state, shcedules) {
      state.schedules = shcedules
      for (let i = 0; i < shcedules.length; i++) {
        state.schedules[i].percent = 0
      }
    },

    /**
     * 初始化用户的建筑数据
     * 将已有建筑数量与建筑清单结合
     * @param state
     * @param buildingList 原始建筑数据
     */
    setBuildingList (state, buildingList) {
      let buildingKeys = Object.keys(buildingList.building)
      let keys = ['farm', 'sawmill']
      for (let i = 0; i < buildingKeys.length; i++) {
        for (let ii = 0; ii < keys.length; ii++) {
          // 检查本条内容是具体建筑
          if (buildingKeys[i].indexOf(keys[ii]) !== -1) {
            let keyNumber = Number(buildingKeys[i].slice(-2)) - 1
            // 将具体内容赋值给建筑清单
            buildingList.list[keys[ii]][keyNumber].number = buildingList.building[buildingKeys[i]]
          }
        }
      }

      state.buildingList = buildingList.list
    },

    /**
     * 初始化用户的资源数据
     * @param state
     * @param resourceData 原始资源数据
     */
    setResource (state, resourceData) {
      // 语义化资源名称
      let resourceTrans = [
        {key: 'people', name: '人口'},
        {key: 'food', name: '食物'},
        {key: 'wood', name: '木材'},
        {key: 'stone', name: '石材'},
        {key: 'money', name: '金钱'},
        {key: 'area', name: '面积'},
      ]
      for (let i = 0; i < resourceTrans.length; i++) {
        state.resource[resourceTrans[i].key] = {
          name: resourceTrans[i].name,
          value: resourceData[resourceTrans[i].key],
          oddment: resourceData[resourceTrans[i].key + 'Chip'],
          output: resourceData[resourceTrans[i].key + 'Output'],
        }
      }
    },
  },
  actions: {
    update (context, interval) {
      setInterval(() => {
        context.commit('setUpdate')
      }, interval)
    }
  },
})
