<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix x">
        <span>STATUS</span>
      </div>
      <el-form :rules="rules" @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="DESCRIPTION" prop="description">
              <el-input v-model="dataForm.description" placeholder="DESCRIPTION"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="START TIME" prop="start">
              <el-time-select
                  style="width: 100%"
                  placeholder="Start time"
                  v-model="dataForm.start"
                  :picker-options="{
                    start: '00:00',
                    end: '10:00'
                  }">
              </el-time-select>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="END TIME" prop="end">
              <el-time-select
                  style="width: 100%"
                  placeholder="End time"
                  v-model="dataForm.end"
                  :picker-options="{
                    start: '00:00',
                    end: '10:00',
                    minTime: dataForm.start
                  }">
              </el-time-select>

            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="COLOR" prop="color">
              <el-color-picker v-model="dataForm.color"></el-color-picker>
            </el-form-item>
          </el-col>
        </el-row>

        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="" prop="active">
              <el-checkbox v-model="dataForm.active">Active</el-checkbox>
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

<script>
    export default {
        name: "status-form",
        props: ["id"],
        data(){
            return {
                labelPosition: "top",
                roles: [],
                dataForm: {
                    description: "",
                    color: "",
                    start: "",
                    end: "",
                    active: true,
                },
                defaultDataForm: {},
                rules: {
                    description: [
                        {
                            required: true,
                            message: "This field is required.",
                            trigger: "blur"
                        }
                    ]
                }
            };
        },
        methods: {
            _save(){
                let params = Object.assign({}, {}, this.dataForm);

                let loadingInstance = this.$loading();

                if(this.id){
                    this.$http
                    .put(`${this.$baseUrl}/api/status/${this.id}`, params)
                    .then(
                        response =>{

                            this.$router.push({
                                name: "status"
                            });

                            this.$notify.success({
                                title: "Notification",
                                dangerouslyUseHTMLString: true,
                                message: "Operation successfully"
                            });

                            // this.storeData(response);
                        },
                        response =>{
                            let messageHtml = "<ul>";
                            let errors = 0;

                            for(var field in response.body.errors){
                                let errorList = response.body.errors[field];
                                errorList.map(error =>{
                                    errors++;
                                    messageHtml += `<li>${error}</li>`;
                                });
                            }

                            messageHtml += "<ul>";

                            let message = `<strong>Error Details (${errors})</strong><br>${messageHtml}`;

                            this.$notify.error({
                                title: "ERROR",
                                dangerouslyUseHTMLString: true,
                                message: message
                            });
                        }
                    )
                    .finally(() =>{
                        loadingInstance.close();
                    });
                }else{
                    this.$http
                    .post(`${this.$baseUrl}/api/status/new`, params)
                    .then(
                        response =>{
                            this.$notify.success({
                                title: "Notification",
                                dangerouslyUseHTMLString: true,
                                message: "Operation successfully"
                            });

                            this.$router.push({
                                name: "status-edit",
                                params: {
                                    id: response.body.id
                                }
                            });
                        },
                        response =>{
                            let messageHtml = "<ul>";

                            let errors = 0;

                            for(var field in response.body.errors){
                                let errorList = response.body.errors[field];
                                errorList.map(error =>{
                                    errors++;
                                    messageHtml += `<li>${error}</li>`;
                                });
                            }

                            messageHtml += "<ul>";

                            let message = `<strong>Error Details (${errors})</strong><br>${messageHtml}`;

                            this.$notify.error({
                                title: "ERROR",
                                dangerouslyUseHTMLString: true,
                                message: message
                            });
                        }
                    )
                    .finally(() =>{
                        loadingInstance.close();
                    });
                }
            },
            submitForm(){
                this.$refs.form.validate(valid =>{
                    if(valid){
                        this._save();
                    }else{
                        this.$notify.error({
                            title: "ERROR",
                            message: "Invalid form."
                        });
                        return false;
                    }
                });
            },
            resetForm(){
                if(this.id){
                    this.dataForm = Object.assign({}, {}, this.defaultDataForm);
                }else{
                    this.$refs.form.resetFields();
                }
            },
            storeData(response){
                this.dataForm = response.body;
                this.dataForm.active = this.dataForm.active === 1 ? true : false;
                this.defaultDataForm = Object.assign({}, {}, this.dataForm);
            },
            fetch(){
                let loadingInstance = this.$loading();

                this.$http
                .get(`${this.$baseUrl}/api/status/${this.id}`)
                .then(
                    response =>{
                        this.roles = response.body.roles;
                        this.storeData(response);
                    },
                    response =>{
                    }
                )
                .finally(() =>{
                    loadingInstance.close();
                });
            }
        },
        mounted(){
            if(this.id){
                this.fetch();
            }
        }
    };
</script>

<style scoped>

</style>
