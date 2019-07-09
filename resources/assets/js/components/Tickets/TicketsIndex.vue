<template>
    <div ref="indexTickets">
        <el-card class="box-card">
            <div slot="header" class="clearfix x">
                <span>MY TICKETS</span>
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
                        <el-form-item label="PORT" prop="type_port_id">
                            <el-select clearable style="width: 100%;" v-model="dataForm.type_port_id"
                                       placeholder="PORT">
                                <el-option v-for="item in typePortOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="DOCUMENT OFFICE" prop="type_document_office_id">
                            <el-select clearable style="width: 100%;" v-model="dataForm.type_document_office_id"
                                       placeholder="DOCUMENT OFFICE">
                                <el-option v-for="item in typeDocumentOfficeOptions" :key="item.id"
                                           :label="item.description"
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
            <div class="mt-1 mb-1">
                <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
            </div>
            <el-table :data="tickets"
                      style="width: 100%"
                      :default-expand-all="false">
                <el-table-column type="expand">
                    <template slot-scope="props">
                        <el-card class="box-card">
                            <div slot="header" class="clearfix">
                                COMMENTS
                            </div>
                            {{props.row.comments}}
                        </el-card>
                    </template>
                </el-table-column>
                <el-table-column width="50"
                                 prop="id" label="ID">
                </el-table-column>
                <el-table-column prop="bl_number" label="BL NUMBER">
                </el-table-column>
                <el-table-column label="STATUS">
                    <template slot-scope="scope, index">
                        <StatusResult :ticket="scope.row"></StatusResult>
                    </template>
                </el-table-column>
                <el-table-column label="CREATED AT" width="200">
                    <template slot-scope="scope, index">
                        <CreatedAtResult :ticket="scope.row">
                        </CreatedAtResult>
                    </template>
                </el-table-column>
                <el-table-column label="CUSTOMER" prop="customer_name">
                </el-table-column>
                <el-table-column label="PAYMENT TYPE" prop="payment_type_description">
                </el-table-column>
                <el-table-column label="PORT" prop="port">
                </el-table-column>
                <el-table-column prop="type_service_description" label="SERVICE TYPE">
                </el-table-column>
                <el-table-column label="MSC USER">
                    <template slot-scope="scope, index">
                        <el-select
                                v-model="scope.row.user_id_assigned"
                                clearable
                                @change="changeUser(scope)"
                                placeholder="Select">
                            <el-option
                                    v-for="employer in employers"
                                    :key="employer.id"
                                    :label="employer.name"
                                    :value="employer.id">
                            </el-option>
                        </el-select>
                    </template>
                </el-table-column>
                <el-table-column label="COMPLETED">
                    <template slot-scope="scope, index">
                        <el-select
                                v-model="scope.row.completed"
                                placeholder=""
                                @change="changeCompleted(scope)">
                            <el-option v-for="item in completedOptions" :key="item.value" :label="item.label"
                                       :value="item.value">
                            </el-option>
                        </el-select>
                    </template>
                </el-table-column>
                <el-table-column label="ACTION" width="180">
                    <template slot-scope="scope, index">
                        <el-button type="primary" icon="el-icon-message" circle
                                   @click="editTicket(scope.row.id, scope.$index)">
                        </el-button>
                        <router-link plain target="_blank"
                                     :to="{name: 'ticket-show', params: {ticket_id: scope.row.id}}">
                            <el-button icon="el-icon-search" circle></el-button>
                        </router-link>
                        <el-button type="danger" icon="el-icon-delete" circle
                                   @click="deleteTicket(scope.row.id, scope.$index)">
                        </el-button>
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
        <dialog-message :dialogVisible.sync="dialogVisible"></dialog-message>
    </div>
</template>

<script src="./TicketsIndex.js">

</script>

<style scoped lang="scss">
    .comment {
        height: 100px;
    }

    .file-uploads {
        margin-bottom: 0px;
    }
</style>
