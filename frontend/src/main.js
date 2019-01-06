/* package */
import qs from 'qs'
import Vue from 'vue'
import axios from 'axios'
import VueAxios from 'vue-axios'
import VueSwal from 'vue-sweetalert2'

/* component */
import App from './App'

/* config */
import router from './router'
import store from './store'

Vue.use(VueSwal)
Vue.use(VueAxios, axios.create({
  baseURL: 'http://www.nice-kingdom.com',
  withCredentials: true,
  transformRequest: [function (data) {
    return qs.stringify(data)
  }],
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded'
  },
}))

Vue.mixin({
  methods: {
    /**
     * VueRouter 页面跳转
     * @param str 页面名称
     */
    jump (str) {
      this.$router.push(str)
    },
    /**
     * 获取当前时间
     * 利用接口数据，同时设定登录数值
     * @param checkLogin 是否检查用户登录状态
     * @returns {number}
     */
    nowTime (checkLogin = false) {
      let timeGap = localStorage.getItem('timeGap')
      if (!timeGap || checkLogin) {
        this.axios.get('index').then((response) => {
          // 存储时间差值
          timeGap = response.data.time - Math.ceil(new Date() / 1000)
          localStorage.setItem('timeGap', timeGap)

          // 存储登录状态
          if (response.data.isLogin && localStorage.getItem('user')) {
            localStorage.setItem('isLogin', 'true')
            localStorage.setItem('heartBeat', response.data.time.toString())
          } else {
            localStorage.setItem('isLogin', 'false')
            localStorage.removeItem('heartBeat')
          }
        }).catch((error) => {
          this.$swal({
            text: (error.response && error.response.data) ? error.response : '检查登录状态失败',
            type: 'error',
          })
        })
      }

      return Math.ceil(new Date() / 1000) + Number(timeGap)
    },
    /**
     * 检查是否登录
     * @returns {string}
     */
    checkLogin () {
      let heartBeat = localStorage.getItem('heartBeat')
      if (!heartBeat || heartBeat < this.nowTime() - 600) {
        // 确定时间差值，并检查用户登录状态
        this.nowTime(true)
      }
      return localStorage.getItem('isLogin') === 'true'
    }
  }
})

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  store,
  components: { App },
  template: '<App/>'
})
