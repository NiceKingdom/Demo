<template>
  <div class="hello">
    <form>
      <label for="email" title="作为登录名">邮 箱</label>
      <input type="text" v-model="loginForm.email" id="email"><br>

      <label for="password" title="8-32 长度">密 码</label>
      <input type="password" v-model="loginForm.password" id="password"><br><br>

      <input type="button" @click="login" value="登  录">
      <router-link tag="button" to="/register">注&nbsp;&nbsp;册</router-link>
    </form>
  </div>
</template>

<script>
export default {
  name: 'Hello',
  data () {
    return {
      msg: 'The king of the kingdom',
      loginForm: {
        email: '',
        password: '',
      }
    }
  },
  methods: {
    login: function () {
      if (!this.loginForm.email || !this.loginForm.password) {
        this.$swal({
          text: '账号或密码未输入',
          type: 'warning',
        })
        return false
      }

      this.axios.post('login', this.loginForm).then((response) => {
        console.info(response.data)
        this.$store.commit('setUser', response.data.user)
        localStorage.setItem('user', JSON.stringify(response.data.user))
        window.location = '/#/manor'
      }).catch((error) => {
        this.$swal({
          text: (error.response.data) ? error.response.data : '服务器出错',
          type: 'error',
        })
      })
    },
  },
  created: function () {
    this.axios.get('index').then((response) => {
      console.info(response.data)
      if (response.data.isLogin && localStorage.getItem('user')) {
        window.location = '/#/manor'
      }
    }).catch((error) => {
      this.$swal({
        text: (error.response && error.response.data) ? error.response : '服务器出错',
        type: 'error',
      })
    })
  },
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
  h1, h2 {
    font-weight: normal;
  }
  ul {
    list-style-type: none;
    padding: 0;
  }
  li {
    display: inline-block;
    margin: 0 10px;
  }
  a {
    color: #42b983;
  }
</style>
