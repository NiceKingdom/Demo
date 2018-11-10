<template>
    <div id="app" class="container">
        <Header />
        <router-view></router-view>
        <Footer />
    </div>
</template>

<script>
  import Header from './components/header/header'
  import Footer from './components/footer/footer'

  export default {
    name: 'App',
    components: {
      Header,
      Footer
    },
  }
</script>

<style type="text/scss" scoped>
</style>
