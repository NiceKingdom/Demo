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
    resource: {},
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
  getters: {
    doneTodos: state => {
      return state.todos.filter(todo => todo.done)
    },
    getTodoById: (state) => (id) => {
      return state.todos.find(todo => todo.id === id)
    }
  },
  mutations: {
    increment: state => state.count++,
    decrement: state => state.count--,

    // 分支：resource-updata 领地的资源，会随时间匀速增长（函数未完成）
    secUpdate: (state) => {
      // https://stackoverflow.com/questions/32422867/when-do-i-need-to-use-hasownproperty
      Object.keys(state.resource).forEach(function (key) {
        let prod = state.resource[key].output * 1
        prod = (prod + '').split('.')
        state.resource[key].value += Number(prod[0])
        state.resource[key].oddment += Number(prod[1])
        if (state.resource[key].oddment > 2.5) {
          prod = (state.resource[key].oddment + '').split('.')
          state.resource[key].value += Number(prod[0])
          state.resource[key].oddment += Number(prod[1])
        }
      })
    },

    /* 设定 */
    setUser (state, userData) {
      state.loginName = userData.nickname
      state.nickname = userData.nickname
      state.kingdom = userData.kingdom
      let capital = userData.capital.split(',')
      state.capitalX = capital[0]
      state.capitalY = capital[1]
    },

    setSchedules (state, shcedules) {
      state.schedules = shcedules
      for (let i = 0; i < shcedules.length; i++) {
        state.schedules[i].percent = 0
      }
    },

    setBuildingList (state, buildingList) {
      // 将已有建筑数量与建筑清单结合
      let buildingKeys = Object.keys(buildingList.building)
      let keys = ['farm', 'sawmill']
      for (let i = 0; i < buildingKeys.length; i++) {
        for (let ii = 0; ii < keys.length; ii++) {
          if (buildingKeys[i].indexOf(keys[ii]) !== -1) {
            let keyNumber = Number(buildingKeys[i].slice(-2)) - 1
            buildingList.list[keys[ii]][keyNumber].number = buildingList.building[buildingKeys[i]]
          }
        }
      }

      state.buildingList = buildingList.list
    },

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
      if (interval > Math.ceil(new Date() / 1000) + 15) {
        context.commit('secUpdate')
      }
    }
  },
})
