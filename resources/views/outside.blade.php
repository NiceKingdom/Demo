@extends('layouts.app')

@section('title', '登录哦')
@section('content')
    <el-row type="flex" justify="center">
        <el-col :span="24">
            <div style="text-align:center;">
                <span style="color: #21c3e4;">嘿朋友！注册或登录，唤醒你的领主吧！</span> <br />
                <span style="color: #ff604c">Hello body! sign up or sign in, wake up your lord!</span> <br />
            </div>
        </el-col>
    </el-row>
    <el-row type="flex" justify="center">
        <el-col :span="6">
            <el-tabs v-model="activeName">
                <el-tab-pane label="登录" name="login">
                    <el-form :model="loginForm" :rules="loginRules" ref="loginForm" label-width="90px" label-position="right">
                        <el-form-item label="邮箱" prop="email">
                            <el-input v-model="loginForm.email"></el-input>
                        </el-form-item>
                        <el-form-item label="密码" prop="password">
                            <el-input type="password" v-model="loginForm.password"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <div style="text-align: center;">
                                <el-button type="success" @click="submitForm('loginForm')" round>启程</el-button>
                            </div>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane label="注册" name="register">
                    <el-form :model="registerForm" :rules="registerRules" ref="registerForm" label-width="90px" label-position="right">
                        <el-form-item label="邮箱" prop="email">
                            <el-input v-model="registerForm.email"></el-input>
                        </el-form-item>
                        <el-form-item label="密码" prop="password">
                            <el-input type="password" v-model="registerForm.password"></el-input>
                        </el-form-item>
                        <el-form-item label="领主名讳" prop="nickname">
                            <el-input v-model="registerForm.nickname"></el-input>
                        </el-form-item>
                        <el-form-item label="王国名称" prop="kingdom">
                            <el-input v-model="registerForm.kingdom"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <div style="text-align: center;">
                                <el-button type="success" @click="submitForm('registerForm')" round>启程</el-button>
                            </div>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
            </el-tabs>
        </el-col>
    </el-row>
@endsection

@section('js')
    <script>
      let data = {
        system: {
          gameTime: 0
        },
        activeHeader: '',
        activeName: 'login',
        loginForm: {
          nickname: '',
          kingdom: '',
        },
        registerForm: {
          nickname: '',
          kingdom: '',
          email: '',
          password: '',
        },
        loginRules: {
          email: [{required: true, message: '登录必填'}],
          password: [{required: true, message: '登录必填'}],
        },
        registerRules: {
          email: [
            {required: true, message: '注册必填'},
            {type: 'email', max: 128, message: '账号需为邮箱格式，最长 128 长度'},
          ],
          password: [
            {required: true, message: '注册必填'},
            {min: 8, max: 32, message: '密码长度为 8-32 哦'},
          ],
          nickname: [
            {required: true, message: '你自己的昵称都忘啦？'},
            {min: 1, max: 8, message: '昵称长度为 1-8 哦'},
          ],
          kingdom: [
            {required: true, message: '你的王国叫什么好呢？'},
            {min: 2, max: 12, message: '王国名长度为 2-12。不易政治性过强哦，作者不想去局里面喝茶……'},
          ],
        },
      }
      let methods = {
        submitForm: function (formName) {
          this.$refs[formName].validate((valid) => {
            if (valid) {
              let url = '/login'
              if (formName !== 'loginForm') {
                url = '/register'
              }

              this.post(url, this.$data[formName], '', function(response) {
                localStorage.setItem('user', JSON.stringify(response))
                app.get('building/list', '', function(response) {
                  localStorage.setItem('building', JSON.stringify(response))
                  location.reload()
                })
              })
            } else {
              return false;
            }
          })
        },
      }
    </script>
@endsection
