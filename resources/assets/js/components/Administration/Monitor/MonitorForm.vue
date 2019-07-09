<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix x">
                <span>MONITOR</span>
            </div>
            <el-form :label-position="labelPosition" ref="form">
                <el-row :gutter="20">
                    <el-col :span="4">
                        <el-form-item label="Pending Jobs">
                            <strong>{{jobs.length}}</strong>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Failed Jobs">
                            <span class="text-danger">
                                <strong>{{failedJobs.length}}</strong>
                            </span>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>
        </el-card>

        <el-card class="box-card mt-3">
            <el-tabs v-model="activeName">
                <el-tab-pane label="Pending Jobs" name="pending_jobs">
                    <el-table :data="jobs" style="width: 100%">
                        <el-table-column type="expand">
                            <template slot-scope="props">
                                <p>Payload:</p>
                                <tree-view :data="props.row.payload"></tree-view>
                            </template>
                        </el-table-column>
                        <el-table-column width="50"
                                         prop="id" label="ID">
                        </el-table-column>
                        <el-table-column prop="queue" label="QUEUE">
                        </el-table-column>
                        <el-table-column label="ATTEMPTS">
                            <template slot-scope="scope, index">
                                {{scope.row.attempts}}
                            </template>
                        </el-table-column>
                        <el-table-column label="RESERVED AT">
                            <template slot-scope="scope, index">
                                {{scope.row.reserved_at}}
                            </template>
                        </el-table-column>
                        <el-table-column label="AVAILABLE AT">
                            <template slot-scope="scope, index">
                                {{scope.row.available_at}}
                            </template>
                        </el-table-column>
                        <el-table-column label="CREATED AT">
                            <template slot-scope="scope, index">
                                {{scope.row.created_at}}
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
                <el-tab-pane label="Failed Jobs" name="failed_jobs">
                    <el-table :data="failedJobs" style="width: 100%">
                        <el-table-column type="expand">
                            <template slot-scope="props">
                                <p>Payload:</p>
                                <tree-view :data="props.row.payload"></tree-view>

                                <p>Exception:</p>
                                <tree-view :data="props.row.exception"></tree-view>
                            </template>
                        </el-table-column>
                        <el-table-column width="50"
                                         prop="id" label="ID">
                        </el-table-column>
                        <el-table-column prop="connection" label="CONNECTION">
                        </el-table-column>
                        <el-table-column label="QUEUE">
                            <template slot-scope="scope, index">
                                {{scope.row.queue}}
                            </template>
                        </el-table-column>
                        <el-table-column label="FAILET AT">
                            <template slot-scope="scope, index">
                                {{scope.row.failed_at}}
                            </template>
                        </el-table-column>
                    </el-table>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>

<script>
  export default {
    name: "MonitorForm",
    data() {
      return {
        labelPosition: "top",
        activeName: "pending_jobs",
        jobs: [],
        failedJobs: [],
      };
    },
    mounted() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/monitor`)
        .then(
          response => {
            this.jobs = response.body.jobs;
            this.failedJobs = response.body.failed_jobs;
          },
          response => {
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    }
  }
</script>

<style scoped>

</style>