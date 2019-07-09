<template>
    <div ref="indexTickets">
        <el-card class="box-card">
            <div slot="header" class="clearfix x">
                <span>TICKETS REPORT</span>
            </div>
            <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="4">
                        <el-form-item label="TICKED ID" prop="ticket_id">
                            <el-input v-model="dataForm.ticket_id" placeholder="TICKET ID" :maxlength="12"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="BL NUMBER" prop="bl_number">
                            <el-input v-model="dataForm.bl_number" placeholder="BL NUMBER" :maxlength="12"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="CREATED AT RANGE" prop="created_at_range">
                            <el-date-picker
                                    style="width:100%;"
                                    v-model="dataForm.created_at_range"
                                    type="daterange"
                                    unlink-panels
                                    range-separator=""
                                    start-placeholder="Start date"
                                    end-placeholder="End date"
                                    :picker-options="pickerOptions">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="CUSTOMER NAME" prop="customer_name">
                            <el-input v-model="dataForm.customer_name" placeholder="CUSTOMER NAME"
                                      :maxlength="20"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="PAYMENT TYPE" prop="payment_type_id">
                            <el-select style="width:100%;" clearable v-model="dataForm.payment_type_id"
                                       placeholder="PAYMENT TYPE">
                                <el-option v-for="item in paymentTypeOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="4">
                        <el-form-item label="TYPE OF SERVICE" prop="type_service_id">
                            <el-select style="width:100%;" clearable v-model="dataForm.type_service_id"
                                       placeholder="TYPE OF SERVICE">
                                <el-option v-for="item in typeServiceOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="MSC USER" prop="user_id_assigned">
                            <el-select style="width:100%;" clearable ref="user_id_assigned"
                                       v-model="dataForm.user_id_assigned"
                                       :disabled="dataForm.tickets_unassigned"
                                       placeholder="MSC USER">
                                <el-option
                                        v-for="employer in employers"
                                        :key="employer.id"
                                        :label="employer.name"
                                        :value="employer.id">
                                </el-option>
                            </el-select>
                            Unassigned tickets
                            <el-switch
                                    v-model="dataForm.tickets_unassigned"
                                    @change="changeUnassignedTickets">
                            </el-switch>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="COMPLETED" prop="completed">
                            <el-select style="width:100%;" clearable v-model="dataForm.completed"
                                       placeholder="COMPLETED">
                                <el-option v-for="item in completedOptions" :key="item.value" :label="item.label"
                                           :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="PORT" prop="port">
                            <el-select clearable style="width: 100%;" v-model="dataForm.type_port_id" placeholder="PORT">
                                <el-option v-for="item in typePortOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="DOCUMENT OFFICE" prop="type_document_office_id">
                            <el-select clearable style="width: 100%;" v-model="dataForm.type_document_office_id" placeholder="DOCUMENT OFFICE">
                                <el-option v-for="item in typeDocumentOfficeOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="COMMENTS" prop="comments">
                            <el-input type="textarea" v-model="dataForm.comments" placeholder="COMMENTS"></el-input>
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

                <div>
                    <form id="form-temp" target="_blank" :action="urlExportExcel" method="post">
                        <input type="hidden" name="_token" :value="$token">
                        <input type="hidden" v-for="(field, key) in filterFields" :key="key" :value="field.value"
                               :name="field.name">

                        <div class="text-right">
                            <el-button native-type="submmit" plain><i class="far fa-file-excel"></i> EXPORT EXCEL
                            </el-button>
                        </div>
                    </form>
                </div>
            </div>

            <el-table :data="tickets"
                      style="width: 100%">
                <el-table-column width="50"
                                 prop="id" label="ID">
                </el-table-column>
                <el-table-column label="MSC USER">
                    <template slot-scope="scope, index">
                        {{scope.row.user_assigned}}
                    </template>
                </el-table-column>
                <el-table-column prop="bl_number" label="BL NUMBER">
                </el-table-column>
                <el-table-column label="PAYMENT TYPE" prop="payment_type_description">
                </el-table-column>
                <el-table-column label="CUSTOMER" prop="customer_name">
                </el-table-column>
                <el-table-column label="RECEIVED TIME" prop="ticket_created">
                    <template slot-scope="scope, index">
                        {{scope.row.ticket_created_format}}
                    </template>
                </el-table-column>
                <el-table-column label="FINAL TRANSACTION" prop="completed_date">
                    <template slot-scope="scope, index">
                        {{scope.row.completed_date_format}}
                    </template>
                </el-table-column>
                <el-table-column label="MINUTES" prop="minutes_transaction">
                    <template slot-scope="scope, index">
                        {{scope.row.minutes_total}}
                    </template>
                </el-table-column>
            </el-table>

            <div class="mt-1 mb-1">
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

<script>
  import {mapGetters} from 'vuex';
  import _ from "lodash";
  import moment from "moment";

  const defaultData = {
    ticket_id: "",
    bl_number: "",
    created_at_range: "",
    customer_name: "",
    payment_type_id: "",
    type_service_id: "",
    type_document_office_id: "",
    type_port_id: "",
    comments: "",
    user_id_assigned: "",
    completed: "",
    tickets_unassigned: false
  };

  export default {
    name: "report-tickets",
    data() {
      return {
        labelPosition: "top",
        dialogVisible: false,
        dataForm: {...defaultData},
        urlExportExcel: `${this.$baseUrl}/api/tickets/report/export-excel`,
        last_page: 0,
        limitByPage: 0,
        totalRecords: 0,
        pickerOptions: {
          shortcuts: [{
            text: 'Last week',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: 'Last month',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: 'Last 3 months',
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            }
          }]
        },
      };
    },
    computed: {
      ...mapGetters({
        tickets: 'getAllTickets',
        typeServiceOptions: 'getTypeServiceOptions',
        paymentTypeOptions: 'getPaymentTypeOptions',
        completedOptions: 'getCompletedOptions',
        typePortOptions: 'getTypePortOptions',
        typeDocumentOfficeOptions: 'getTypeDocumentOfficeOptions',
        employers: 'getEmployerOptions',
      }),
      filterFields() {
        let filterFields = _.cloneDeep(this.dataForm);
        let data = [];
        for (var field in filterFields) {

          let param = {name: field, value: filterFields[field]};

          if (field === 'created_at_range' && filterFields[field]) {

            let date1Value = moment(filterFields[field][0]).format('YYYY-MM-DD');
            param = {name: 'created_at_start', value: date1Value};
            data.push(param);

            let date2Value = moment(filterFields[field][1]).format('YYYY-MM-DD');
            param = {name: 'created_at_end', value: date2Value};
            data.push(param);

          } else if (field === 'tickets_unassigned') {

            param = {name: field, value: filterFields[field] ? 1 : 0};

            data.push(param);

          } else {

            data.push(param);

          }

        }

        return data;
      },
    },
    methods: {
      changeUnassignedTickets() {
        this.dataForm.user_id_assigned = "";
      },
      changeCompleted(scope) {
        let loadingInstance = this.$loading();

        let params = {
          ticket_id: scope.row.id,
          user_id_assigned: scope.row.user_id_assigned,
          completed: scope.row.completed
        };

        this.$http
          .post(`${this.$baseUrl}/api/tickets/change_to_completed`, params)
          .then(
            () => {
              loadingInstance.close();
            },
            () => {
              this.$notify.error({
                title: "ERROR",
                dangerouslyUseHTMLString: true,
                message: "Error"
              });
            }
          )
          .finally(() => {
            loadingInstance.close();
          });
      },
      changeUser(scope) {
        let loadingInstance = this.$loading();

        let params = {
          ticket_id: scope.row.id,
          user_id_assigned: scope.row.user_id_assigned
        };

        this.$http
          .post(`${this.$baseUrl}/api/tickets/change_assignation`, params)
          .then(
            () => {
              loadingInstance.close();
            },
            () => {
              this.$notify.error({
                title: "ERROR",
                dangerouslyUseHTMLString: true,
                message: "Error"
              });
            }
          )
          .finally(() => {
            loadingInstance.close();
          });
      },
      handleCurrentChange(page) {
        this.dataForm.page = page;
        this.fetch();
      },
      submitForm() {
        this.dataForm.page = null;
        this.fetch();
      },
      resetForm() {
        this.$refs.form.resetFields();
        this.dataForm = {...defaultData};
        this.fetch();
      },
      handleCurrentChange(page) {
        this.dataForm.page = page;
        this.fetch();
      },
      fetch() {
        let params = Object.assign({}, {}, this.dataForm);

        delete params.created_at_range;

        params.created_at_start = "";
        params.created_at_end = "";

        if (this.dataForm.created_at_range) {
          params.created_at_start = moment(this.dataForm.created_at_range[0]).format('YYYY-MM-DD');
          params.created_at_end = moment(this.dataForm.created_at_range[1]).format('YYYY-MM-DD');
        }

        params.tickets_unassigned = params.tickets_unassigned ? 1 : 0;

        let loadingInstance = this.$loading();

        this.$http
          .get(`${this.$baseUrl}/api/tickets`, {params})
          .then(
            response => {
              loadingInstance.close();
              let tickets = response.body.data;
              this.$store.commit('refreshTickets', tickets);
              this.last_page = response.body.last_page;
              this.limitByPage = response.body.per_page;
              this.totalRecords = response.body.total;
            },
            () => {
              this.$notify.error({
                title: "ERROR",
                dangerouslyUseHTMLString: true,
                message: "Error"
              });
            }
          )
          .finally(() => {
            loadingInstance.close();
          });
      },
      editTicket(id) {
        let ticket = this.tickets.find(ticket => ticket.id === id);
        this.$store.commit('setCurrentTicket', _.cloneDeep(ticket));
        this.dialogVisible = true;
      },
      deleteTicket(id, index) {
        this.$confirm("Are you sure to delete this ticket?")
          .then(sure => {
            if (sure) {
              const url = `${this.$baseUrl}/api/ticket/delete/${id}`;

              let loadingInstance = this.$loading();
              this.$http
                .delete(url, {})
                .then(
                  response => {
                    if (response && response.body === true) {
                      this.tickets.splice(index, 1);
                      let message = "Ticket deleted successfully";
                      this.$message.success(message);
                    } else {
                      this.$message.error(response.body);
                    }
                  },
                  () => {
                    this.$message.error("Error deleting ticket");
                  }
                )
                .finally(() => {
                  loadingInstance.close();
                });
            }
          })
          .catch(() => {
          });
      },
    },
    mounted() {
      this.$store.dispatch('fetchOptions');
      this.fetch();
    },
  };
</script>

<style scoped lang="scss">
    .comment {
        height: 100px;
    }

    .file-uploads {
        margin-bottom: 0px;
    }
</style>
