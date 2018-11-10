<template>
    <div class="register">
        <form>
            <label for="email" title="登录名">邮 箱</label>
            <input type="email" v-model="signUpForm.email" id="email"><br>

            <label for="password" title="8-32 长度">密 码</label>
            <input type="password" v-model="signUpForm.password" id="password"><br>

            <label for="kingdom" title="最大 12 个字">王 国</label>
            <input type="text" v-model="signUpForm.kingdom" id="kingdom"><br>

            <label for="nickname" title="最大 16 个字">昵 称</label>
            <input type="text" v-model="signUpForm.nickname" id="nickname"><br><br>

            <!--<label for="password" title="选择其一">国 家</label>-->
            <!--<input type="password" v-model="signUpForm.password" id="password"><br><br>-->

            <input type="button" @click="signUp" value="注 册">
            <router-link tag="button" to="/">登&nbsp;&nbsp;录</router-link>
        </form>
    </div>
</template>

<script>
  export default {
    name: 'register',
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
      signUp: function () {
        if (!this.signUpForm.email || !this.signUpForm.password || !this.signUpForm.kingdom ||
            !this.signUpForm.nickname) {
          this.$swal({
            text: '请完整输入信息',
            type: 'warning',
          })
          return false
        }

        this.axios.post('register', this.signUpForm).then((response) => {
          console.info(response.data)
          let result = response.data
          this.$store.commit('setUser', result.user)
          localStorage.setItem('user', JSON.stringify(result.user))
          window.location = '/#/manor'
        }).catch((error) => {
          this.$swal({
            text: (error.response.data) ? error.response.data : '服务器出错',
            type: 'error',
          })
        })
      },
    }
  }
</script>

<style scoped>

</style>
