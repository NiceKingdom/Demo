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

/* 全局混入，慎用 */
Vue.mixin({
  methods: {
    jump (str) {
      this.$router.push(str)
    },
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
