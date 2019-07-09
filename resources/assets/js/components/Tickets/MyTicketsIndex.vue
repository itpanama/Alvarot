<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>MY TICKETS</span>
            </div>
            <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="TICKED ID" prop="ticket_id">
                            <el-input v-model="dataForm.ticket_id" placeholder="TICKET ID" :maxlength="12"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="BL NUMBER" prop="bl_number">
                            <el-input v-model="dataForm.bl_number" placeholder="BL NUMBER" :maxlength="12"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="TYPE OF SERVICE" prop="type_service_id">
                            <el-select clearable v-model="dataForm.type_service_id" placeholder="TYPE OF SERVICE">
                                <el-option v-for="item in typeServiceOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
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
                <router-link style="float: right;" :to="{name: 'ticket-new'}">
                    <i class="el-icon-circle-plus"></i> New Ticket
                </router-link>
            </div>
            <el-table :data="tickets">
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
                <el-table-column prop="id" label="TICKED ID">
                </el-table-column>
                <el-table-column prop="bl_number" label="BL NUMBER">
                </el-table-column>
                <el-table-column label="CREATED AT" width="200">
                    <template slot-scope="scope, index">
                        <CreatedAtResult :ticket="scope.row"></CreatedAtResult>
                    </template>
                </el-table-column>
                <el-table-column label="PAYMENT TYPE" prop="payment_type_description">
                </el-table-column>
                <el-table-column label="PORT" prop="port">
                </el-table-column>
                <el-table-column prop="type_service_description" label="SERVICE">
                </el-table-column>
                <el-table-column label="MSC USER">
                    <template slot-scope="scope, index">
                        <strong>{{scope.row.user_assigned}}</strong>
                    </template>
                </el-table-column>
                <el-table-column label="COMPLETED">
                    <template slot-scope="scope, index">
                        <div>
                            <span v-if="scope.row.completed">Yes</span>
                            <span v-else>No</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column label="ACTION">
                    <template slot-scope="scope, index">
                        <router-link plain target="_blank"
                                     :to="{name: 'ticket-show', params: {ticket_id: scope.row.id}}">
                            <el-button icon="el-icon-search" circle></el-button>
                        </router-link>
                        <el-button v-if="!scope.row.completed" type="danger" icon="el-icon-delete" circle
                                   @click="deleteTicket(scope.row.id, scope.$index)"></el-button>
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

<script src="./MyTicketsIndex.js">

</script>

<style scoped>
</style>