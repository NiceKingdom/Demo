window.Vue = require('vue');

import Vue from 'vue'

import App from './App'
import Outside from './components/outside/outside'
import Home from './components/home/home'
import Building from './components/building/building'

import VueX from 'vuex'
Vue.use(VueX)
import VueRouter from 'vue-router'
Vue.use(VueRouter)
import VueResource from 'vue-resource'
Vue.use(VueResource)
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
Vue.use(ElementUI)

/* Data init */
let router = ''
let store = ''

if (localStorage.getItem('loginCheck') === 'true') {
  /* Router */
  router = new VueRouter({
    routes: [
      { path: '/', component: Home },
      { path: '/home', component: Home },
      { path: '/building', component: Building },
    ]
  })
  /* VueX */
  let data = localStorage.getItem('user') ? JSON.parse(localStorage.getItem('user')) : false;
  store = new VueX.Store({
    state: {
      user: {
        id: (data) ? data.user.id : 0,
        nickname: (data) ? data.user.nickname : '异常',
        kingdom: (data) ? data.user.kingdom : '异常',
        capital: (data) ? data.user.capital : 'x,y',
      },
      system: {
        gameTime: (data) ? data.system.gameTime : 5528682,
        pack: (data) ? data.system.pack : '异常',
        version: (data) ? data.system.version : 0.00,
      },
      building: {
        farm01: (data) ? data.building.people : 0,
        farm02: (data) ? data.building.people : 0,
        sawmill01: (data) ? data.building.people : 0,
        sawmill02: (data) ? data.building.people : 0,
      },
      resourceName: {
        'people': '人口',
        'food': '食物',
        'wood': '木材',
        'stone': '石块',
        'money': '钱财',
      },
      resource: {
        people: (data) ? data.resource.people : 0,
        food: (data) ? data.resource.food : 0,
        wood: (data) ? data.resource.wood : 0,
        stone: (data) ? data.resource.stone : 0,
        money: (data) ? data.resource.money : 0,
      },
      resourceOutput: {
        food: (data) ? data.resource.foodOutput : 0,
        wood: (data) ? data.resource.woodOutput : 0,
        money: (data) ? data.resource.moneyOutput : 0,
        stone: (data) ? data.resource.stoneOutput : 0,
      },
    },
    mutations: {
      resourceUpdate: (state, params) => {
        if (params.vector === 'addition') {
          state.resource[params.item] += params.value
        } else if (params.vector === 'subtraction') {
          state.resource[params.item] -= params.value
        }
      },
    }
  })
} else {
  /* Router */
  router = new VueRouter({
    routes: [
      { path: '/', component: Outside },
      { path: '/home', component: Outside },
    ]
  })
  /* VueX */
  store = new VueX.Store({})
}

// root element
new Vue({
  components: {App},
  template: '<App/>',
  router,
  store
}).$mount('#app')
