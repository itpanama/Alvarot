<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix x">
        <span>CUSTOMERS</span>
      </div>
      <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="NAME" prop="name">
              <el-input v-model="dataForm.name" placeholder="NAME"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="EMAIL" prop="email">
              <el-input v-model="dataForm.email" placeholder="EMAIL"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
  
        <el-form-item>
          <el-button type="primary" icon="el-icon-search" v-on:click.prevent="submitForm()">FILTER</el-button>
          <el-button v-on:click.prevent="resetForm()">RESET</el-button>
        </el-form-item>
  
      </el-form>
    </el-card>
  
    <el-card class="box-card mt-3">
      <div slot="header" class="clearfix">
        <div class="float-left">
          <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
        </div>

        <router-link class="float-right" :to="{name: 'customer-new'}">
          <i class="el-icon-circle-plus"></i> New Customer
        </router-link>
      </div>
      <el-table :data="customers" style="width: 100%">
        <el-table-column width="50"
          prop="id" label="ID">
        </el-table-column>
        <el-table-column prop="name" label="NAME">
        </el-table-column>
        <el-table-column prop="email" label="EMAIL">
        </el-table-column>
        <el-table-column label="ACTION" width="180">
          <template slot-scope="scope, index">
              <router-link plain :to="{name: 'customer-edit', params: {id: scope.row.id}}">
                  <el-button type="primary" icon="el-icon-edit" circle></el-button>
              </router-link>
              <el-button type="danger" icon="el-icon-delete" circle @click="deleteUser(scope.row.id, scope.$index)"></el-button>
          </template>
        </el-table-column>
      </el-table>

      <div class="mt-1">
        <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
      </div>

      <el-pagination
        class="mt-2 mb-2"
        :page-size="limitByPage"
        :page-count="last_page"
        @current-change="handleCurrentChange"
        layout="prev, pager, next">
      </el-pagination>
    </el-card>
  </div>
</template>

<script src="./CustomersIndex.js">
  
</script>

<style scoped lang="scss"></style>
