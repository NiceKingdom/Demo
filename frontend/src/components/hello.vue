<template>
  <div>
    <div class="login-wrapper">
      <div class="form-box-wrapper">
        <div class="logo-wrapper">
          <img src="../assets/logo.png" alt="繁盛王国" class="logo" width="100%" height="auto"/>
        </div>
        <div class="username-box">
          <span class="tittle">用户名:</span>
          <input type="email" class="input" v-model="loginForm.email" @keyup.enter="loginOperate">
        </div>
        <div class="password-box">
          <span class="tittle">密 码:</span>
          <input type="password" class="input" v-model="loginForm.password" @keyup.enter="loginOperate" />
        </div>
        <div class="buttons-wrapper">
          <button class="login-btn" @click="loginOperate">登 录</button>
          <button class="register-btn" @click="jump('register')">注 册</button>
        </div>
      </div>
    </div>
    <VFooter />
  </div>
</template>

<script>
  import VFooter from './sub/v-footer'

  export default {
    name: 'Hello',
    components: { VFooter },
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
      loginOperate: function () {
        if (!this.loginForm.email || !this.loginForm.password) {
          this.$swal({
            text: '账号或密码未输入',
            type: 'warning',
          })
          return false
        }

        this.axios.post('login', this.loginForm).then((response) => {
          this.$store.commit('setUser', response.data.user)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          localStorage.setItem('resource', JSON.stringify(response.data.resource))
          localStorage.setItem('building', JSON.stringify(response.data.building))
          localStorage.setItem('heartBeat', Math.ceil(new Date() / 1000).toString())
          localStorage.setItem('isLogin', 'true')
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
        if (response.data.isLogin && localStorage.getItem('user')) {
          localStorage.setItem('heartBeat', Math.ceil(new Date() / 1000).toString())
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
<style scoped lang="stylus">
  .login-wrapper
    position: relative
    width: 100%
    min-height 87.88vh
    .form-box-wrapper
      margin: auto
      position: absolute
      left: 0
      right: 0
      top: -120px
      bottom: 0
      width: 500px
      height: 360px
      background-color #ffffff
      border: 1px solid #cccccc
      border-bottom-left-radius: 10px
      border-bottom-right-radius: 10px
      .logo-wrapper
        margin-bottom: 20px
        width: 100%
        height: auto
        .logo
          width: 100%
          height:auto
      .username-box,.password-box
        margin-top: 2rem
        width: 100%
        font-size: 0
        .tittle
          display: inline-block
          margin-right: 2rem
          width: 3.6em
          height: 1.6em
          line-height: 1.6em
          font-size: 1.2rem
          text-align: right
        .input
          padding: 0 .2rem
          width: 16rem
          height: 1.6em
          line-height: 1.6em
          font-size: 1.2rem
          border: 1px solid #cccccc
          -webkit-box-shadow: none
          -moz-box-shadow: none
          box-shadow: none
          outline: none
      .buttons-wrapper
        margin-top: 3.2rem
        width: 100%
        .login-btn,.register-btn
          width: 4em
          height: 2em
          line-height: 2em
          font-size: 1rem
          font-family: '微软雅黑'
          text-align: center
          border: none
          color: #ffffff
          -webkit-border-radius: .2rem
          -moz-border-radius: .2rem
          border-radius: .2rem
          background-color: #0D293E
        .login-btn
          margin-right: 2rem
</style>
