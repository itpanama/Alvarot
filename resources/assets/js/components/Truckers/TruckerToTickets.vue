<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>TICKETS</span>
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
                </el-row>

                <el-form-item>
                    <el-button type="primary" icon="el-icon-search" v-on:click.prevent="submitForm()">FILTER</el-button>
                    <el-button v-on:click.prevent="resetForm()">RESET</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="box-card mt-3">
            <el-table :data="tickets">
                <el-table-column prop="id" label="TICKED ID">
                </el-table-column>
                <el-table-column prop="bl_number" label="BL NUMBER">
                </el-table-column>
                <el-table-column label="PAYMENT TYPE" prop="payment_type_description">
                </el-table-column>
                <el-table-column label="PORT" prop="port">
                </el-table-column>
                <el-table-column prop="type_service_description" label="SERVICE">
                </el-table-column>
                <el-table-column label="MSC USER">
                    <template slot-scope="scope, index">
                        {{scope.row.user_assigned}}
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
<script src="./TruckerToTickets.js"></script>
<style scoped lang="scss"></style>
