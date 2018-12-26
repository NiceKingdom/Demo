import Vue from 'vue'
import Router from 'vue-router'
import Hello from '@/components/hello'
import Register from '@/components/register'
import Manor from '@/components/manor'
import Building from '@/components/building'
import Plat from '@/components/plat'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'Hello',
      component: Hello
    }, {
      path: '/manor',
      name: 'Manor',
      component: Manor
    }, {
      path: '/register',
      name: 'Register',
      component: Register
    }, {
      path: '/building',
      name: 'Building',
      component: Building
    }, {
      path: '/plat',
      name: 'Plat',
      component: Plat
    },
  ]
})
