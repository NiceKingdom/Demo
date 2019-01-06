<template>
    <!--<div class="register">-->
        <!--<form>-->
            <!--<label for="email" title="登录名">邮 箱</label>-->
            <!--<input type="email" v-model="signUpForm.email" id="email"><br>-->

            <!--<label for="password" title="8-32 长度">密 码</label>-->
            <!--<input type="password" v-model="signUpForm.password" id="password"><br>-->

            <!--<label for="kingdom" title="最大 12 个字">王 国</label>-->
            <!--<input type="text" v-model="signUpForm.kingdom" id="kingdom"><br>-->

            <!--<label for="nickname" title="最大 16 个字">昵 称</label>-->
            <!--<input type="text" v-model="signUpForm.nickname" id="nickname"><br><br>-->

            <!--&lt;!&ndash;<label for="password" title="选择其一">国 家</label>&ndash;&gt;-->
            <!--&lt;!&ndash;<input type="password" v-model="signUpForm.password" id="password"><br><br>&ndash;&gt;-->

            <!--<input type="button" @click="signUp" value="注 册">-->
            <!--<router-link tag="button" to="/">登&nbsp;&nbsp;录</router-link>-->
        <!--</form>-->
    <!--</div>-->
  <div>
    <div class="register-wrapper">
      <div class="form-box-wrapper">
        <div class="logo-wrapper">
          <img src="../assets/logo.png" alt="繁盛王国" class="logo" width="100%" height="auto"/>
        </div>
        <div class="username-box">
          <span class="tittle">邮 箱:</span>
          <input type="email" class="input" v-model="signUpForm.email" @keyup.enter="registerOperate">
        </div>
        <div class="password-box">
          <span class="tittle">密 码:</span>
          <input type="password" class="input" v-model="signUpForm.password" @keyup.enter="registerOperate">
        </div>
        <div class="kingdom-box">
          <span class="tittle">王 国:</span>
          <input type="text" class="input" v-model="signUpForm.kingdom" @keyup.enter="registerOperate">
        </div>
        <div class="lord-box">
          <span class="tittle">昵 称:</span>
          <input type="text" class="input" v-model="signUpForm.nickname" @keyup.enter="registerOperate">
        </div>

        <div class="buttons-wrapper">
          <button class="register-btn" @click="registerOperate">注 册</button>
          <button class="login-btn" @click="jump('/')">登 录</button>
        </div>
      </div>
    </div>
    <VFooter />
  </div>
</template>

<script>
  import VFooter from './sub/v-footer'

  export default {
    name: 'register',
    components: { VFooter },
    data () {
      return {
        signUpForm: {
          email: '',
          password: '',
          kingdom: '',
          nickname: '',
        }
      }
    },
    methods: {
      registerOperate: function () {
        if (!this.signUpForm.email || !this.signUpForm.password || !this.signUpForm.kingdom ||
            !this.signUpForm.nickname) {
          this.$swal({
            type: 'warning',
            text: '请完整输入信息',
          })
          return false
        }

        this.axios.post('register', this.signUpForm).then((response) => {
          this.$store.commit('setUser', response.data.user)
          localStorage.setItem('user', JSON.stringify(response.data.user))
          localStorage.setItem('resource', JSON.stringify(response.data.resource))
          localStorage.setItem('building', JSON.stringify(response.data.building))
          localStorage.setItem('heartBeat', Math.ceil(new Date() / 1000).toString())
          localStorage.setItem('isLogin', 'true')
          window.location = '/#/manor'
        }).catch((error) => {
          this.$swal({
            type: 'error',
            text: (error.response.data) ? error.response.data : '服务器出错',
          })
        })
      },
    },
    created: function () {
      // 确定时间差值，并检查用户登录状态
      if (this.checkLogin()) {
        window.location = '/#/manor'
      }
    },
  }
</script>

<style scoped lang="stylus">
  .register-wrapper
    position: relative
    width: 100%
    min-height: 87.88vh
    .form-box-wrapper
      margin: auto
      position: absolute
      left: 0
      right: 0
      top: 0
      bottom: 0
      width: 500px
      height: 480px
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
      .username-box,.password-box,.kingdom-box,.lord-box
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
        .register-btn
          margin-right: 2rem
</style>
