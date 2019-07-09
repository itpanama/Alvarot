<template>
  <div>
    <el-form :rules="rules" @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
      <el-card class="box-card">
        <div slot="header" class="clearfix x">
          <span>CUSTOMER</span>
        </div>
  
        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="NAME" prop="name">
              <el-input v-model="dataForm.name" placeholder="NAME"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="EMAIL 1" prop="email">
              <el-input v-model="dataForm.email" placeholder="EMAIL 1 "></el-input>
            </el-form-item>
          </el-col>
  
          <el-col :span="8">
            <el-form-item label="EMAIL 2" prop="email_optional">
              <el-input v-model="dataForm.email_optional" placeholder="EMAIL 2"></el-input>
            </el-form-item>
          </el-col>
        </el-row>
      </el-card>
  
      <el-card class="box-card mt-3">
        <div slot="header" class="clearfix">
          <span>Login Credentials</span>
        </div>
        <div class="item">
          <el-row :gutter="20">
            <el-col :span="8">
              <el-form-item label="USERNAME" prop="username">
                <el-input v-model="dataForm.username" placeholder="USERNAME"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="STATUS" prop="active">
                <el-checkbox v-model="dataForm.active">Active</el-checkbox>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="Trucker Form" prop="trucker_form">
                <el-checkbox v-model="dataForm.trucker_form">Active</el-checkbox>
              </el-form-item>
            </el-col>
          </el-row>
  
          <el-row :gutter="20">
            <el-col :span="8">
              <el-form-item label="PASSWORD" prop="password">
                <el-input type="password" v-model="dataForm.password" placeholder="PASSWORD"></el-input>
              </el-form-item>
            </el-col>
            <el-col :span="8">
              <el-form-item label="CONFIRM PASSWORD" prop="password_confirmation">
                <el-input type="password" v-model="dataForm.password_confirmation" placeholder="PASSWORD CONFIRMATION"></el-input>
              </el-form-item>
            </el-col>
          </el-row>
        </div>
      </el-card>
  
      <el-card class="box-card mt-3">
        <div class="item">
          <el-form-item class="mt-4">
            <el-button type="primary" v-on:click.prevent="submitForm()">SUBMIT</el-button>
            <el-button v-on:click="resetForm()">RESET</el-button>
          </el-form-item>
        </div>
      </el-card>
  
    </el-form>
  
  </div>
</template>

<script src="./CustomerForm.js">
  
</script>

<style scoped lang="scss"></style>
