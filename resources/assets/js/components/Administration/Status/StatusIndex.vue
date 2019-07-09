<template>
  <div>
    <el-card class="box-card">
      <div slot="header" class="clearfix x">
        <span>STATUS</span>
      </div>
      <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
        <el-row :gutter="20">
          <el-col :span="8">
            <el-form-item label="DESCRIPTION" prop="description">
              <el-input v-model="dataForm.description" placeholder="DESCRIPTION"></el-input>
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item label="ACTIVE" prop="active">
              <el-select clearable v-model="dataForm.active" style="width:100%;">
                <el-option v-for="(item, key) in active" :key="key" :label="item.description" :value="item.active">
                </el-option>
              </el-select>
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
        <router-link style="float: right;" :to="{name: 'status-new'}">
          <i class="el-icon-circle-plus"></i> New Status
        </router-link>
      </div>
      <el-table :data="status" style="width: 100%">
        <el-table-column width="50"
                         prop="id" label="ID">
        </el-table-column>
        <el-table-column prop="description" label="DESCRIPTION">
        </el-table-column>
        <el-table-column label="COLOR">
          <template slot-scope="scope, index">
            <div :style="{backgroundColor: scope.row.color, height: '20px', width: '100px'}"></div>
          </template>
        </el-table-column>
        <el-table-column prop="start" label="START TIME">
        </el-table-column>
        <el-table-column prop="end" label="END TIME">
        </el-table-column>
        <el-table-column label="ACTION?" width="180">
          <template slot-scope="scope, index">
            <span v-if="scope.row.active">Active</span>
            <span v-else>Inactive</span>
          </template>
        </el-table-column>
        <el-table-column label="ACTION" width="180">
          <template slot-scope="scope, index">
            <router-link plain :to="{name: 'status-edit', params: {id: scope.row.id}}">
              <el-button type="primary" icon="el-icon-edit" circle></el-button>
            </router-link>
            <el-button v-if="scope.row.system !== 1" type="danger" icon="el-icon-delete" circle @click="deleteStatus(scope.row.id, scope.$index)"></el-button>
          </template>
        </el-table-column>
      </el-table>

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

<script>
    export default {
        name: "customers-index",
        data(){
            return {
                labelPosition: "top",
                dataForm: {
                    description: "",
                    active: ""
                },
                active: [
                    { active: 1, description: "Active" },
                    { active: 0, description: "Inactive" }
                ],
                roles: [],
                status: [],
                last_page: 0,
                limitByPage: 0
            };
        },
        methods: {
            submitForm(){
                this.fetch();
            },
            resetForm(){
                this.$refs.form.resetFields();
                this.dataForm.page = null;
                this.fetch();
            },
            handleCurrentChange(page){
                this.dataForm.page = page;
                this.fetch();
            },
            fetch(){
                let params = Object.assign({}, {}, this.dataForm);
                let loadingInstance = this.$loading();
                this.$http
                .get(`${this.$baseUrl}/api/status`, {params})
                .then(
                    response =>{
                        loadingInstance.close();
                        this.status = response.body.data;
                        this.last_page = response.body.last_page;
                        this.limitByPage = response.body.per_page;
                    },
                    () =>{
                        this.$notify.error({
                            title: "ERROR",
                            dangerouslyUseHTMLString: true,
                            message: "Error"
                        });
                    }
                )
                .finally(() =>{
                    loadingInstance.close();
                });
            },
            deleteStatus(id, index){
                this.$confirm("Are you sure to delete this status?")
                .then(sure =>{
                    if(sure){
                        const url = `${this.$baseUrl}/api/status/delete/${id}`;

                        let loadingInstance = this.$loading();
                        this.$http
                        .delete(url, {})
                        .then(
                            response =>{
                                if(response && response.body === true){
                                    this.status.splice(index, 1);
                                    let message = "Status deleted successfully";
                                    this.$message.success(message);
                                }else{
                                    this.$message.error(response.body);
                                }
                            },
                            error =>{
                                this.$message.error("Error deleting status");
                            }
                        )
                        .finally(() =>{
                            loadingInstance.close();
                        });
                    }
                })
                .catch(_ =>{
                });
            }
        },
        mounted(){
            this.fetch();
        }
    };
</script>

<style scoped>

</style>