<template>
  <div>
    <el-header height="auto">
      <div class="bg-warning">
        <el-row :gutter="12">
          <el-col :xs="12" :sm="12" :md="12" :lg="12" :xl="12">
            <div class="p-2">
              <router-link class="navbar-brand" to="/">
                <img src="img/logo.png" class="logo">
                <span class="d-none d-md-inline text-dark">MSC PANAMA COUNTER TICKETING</span>
              </router-link>
            </div>
          </el-col>
          <el-col :xs="8" :sm="12" :md="12" :lg="12" :xl="12">
            <div class="text-dark d-flex flex-row-reverse p-2 text-right">Hi, {{userLogin.name}}</div>
          </el-col>
        </el-row>
        <header-navegation :user-login="userLogin"></header-navegation>
      </div>
    </el-header>
    <el-container>
      <el-main>
        <router-view></router-view>
      </el-main>
    </el-container>
  </div>
</template>

<script src="./App.js">

</script>

<style scoped lang="scss" src="./App.scss">

</style>
