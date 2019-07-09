<template>
  <div>
    <el-menu :default-active="activeIndex"  class="el-menu-demo" mode="horizontal" @select="handleSelect" background-color="#545c64" text-color="#fff" active-text-color="#ffd04b">
      <!--<el-menu-item index="/">Home</el-menu-item>-->
      <el-menu-item v-if="hasPermissionToTruckerModule" index="trucker-to-tickets">
        <i class="fas fa-list"></i> Asignados
      </el-menu-item>
      <el-menu-item v-if="isAdminOrEmployerUserLogged" index="tickets"><i class="fas fa-list"></i> Tickets</el-menu-item>
      <el-menu-item v-if="isCustomerUserLogged" index="my-tickets"><i class="fas fa-list"></i> My Tickets</el-menu-item>
      <el-menu-item v-if="isCustomerUserLogged" index="ticket-new"><i class="fas fa-plus"></i> New Ticket</el-menu-item>
      <el-menu-item v-if="hasPermissionToTruckerModule" index="trucker-request"><i class="fa fa-list-alt" aria-hidden="true"></i> Ficha del transportista</el-menu-item>
      <!--<el-menu-item v-if="isAdminOrEmployerUserLogged" index="/delay-new"><i class="fas fa-calculator"></i> Empty Return Calculations </el-menu-item>-->
      <!--<el-menu-item v-if="isAdminOrEmployerUserLogged" index="/calculate-release-new"><i class="fas fa-calculator"></i> Calculator Release</el-menu-item>-->
      <el-menu-item index="/my-msc" v-if="!isTruckerUserLogged">
        <a target="_blank" class="text-white" href="https://www.mymsc.com/Link/">
          <i class="fas fa-link"></i> My MSC
        </a>
      </el-menu-item>
      <el-submenu v-if="isAdminOrEmployerUserLogged" index="4">
        <template slot="title">
          <i class="fas fa-file-contract"></i> Tools
        </template>
        <el-menu-item index="customers">
          Customers
        </el-menu-item>
        <el-menu-item index="users">
          Users
        </el-menu-item>
        <el-menu-item index="truckers">
          Truckers
        </el-menu-item>
        <el-menu-item index="newsletter">
          Newsletter
        </el-menu-item>
        <hr>
        <el-menu-item index="status">
          Status
        </el-menu-item>
        <el-menu-item index="settings">
          Settings
        </el-menu-item>
        <el-menu-item index="monitor">
          Monitor
        </el-menu-item>
      </el-submenu>
      <el-submenu v-if="isAdminOrEmployerUserLogged" index="5">
        <template slot="title">
          <i class="fas el-icon-date"></i> Reports
        </template>
        <el-menu-item index="report-tickets"><i class="fas el-icon-tickets"></i> Tickets</el-menu-item>
      </el-submenu>
      <el-menu-item index="" v-on:click="logout()" class="float-right">
        <i class="fas fa-sign-out-alt"></i> <span v-if="isTruckerUserLogged">Cerrar sesión</span><span v-else>Logout</span>
      </el-menu-item>
    </el-menu>
    <form id="logout-form" :action="urlLogout" method="POST" style="display: none;">
      <input type="hidden" name="_token" :value="token">
    </form>
  </div>
</template>

<script src="./HeaderNavegation.js">
</script>

<style scoped lang="scss" src="./HeaderNavegation.scss">
</style>
