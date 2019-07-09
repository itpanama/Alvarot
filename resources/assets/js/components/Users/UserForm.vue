<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix x">
        <span>USER</span>
      </div>
      <el-form :rules="rules" @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
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
          <el-col :span="8">
            <el-form-item label="USERNAME" prop="username">
              <el-input v-model="dataForm.username" placeholder="USERNAME"></el-input>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="ROLE" prop="role_id">
              <el-select clearable v-model="dataForm.role_id" placeholder="ROLE" style="width:100%;">
                <el-option v-for="item in roles" :key="item.id" :label="item.description" :value="item.id">
                </el-option>
              </el-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="STATUS" prop="active">
              <el-checkbox v-model="dataForm.active">Active</el-checkbox>
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

        <el-form-item class="mt-4">
          <el-button type="primary" v-on:click.prevent="submitForm()">SUBMIT</el-button>
          <el-button v-on:click="resetForm()">RESET</el-button>
        </el-form-item>
  
      </el-form>
    </el-card>
  </div>
</template>

<script src="./UserForm.js">
  
</script>

<style scoped lang="scss" src="./UserForm.scss">
  
</style>
