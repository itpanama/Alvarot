<template>
    <div>
        <el-card class="box-card">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item>Ticket {{ticketForm.id}}</el-breadcrumb-item>
            </el-breadcrumb>
        </el-card>

        <el-card class="box-card mt-3">
            <el-form :label-position="labelPosition" ref="form">
                <h3><i class="el-icon-tickets"></i> Ticket Summary</h3>
                <hr>
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="BL NUMBER">
                            {{ticketForm.bl_number}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="CUSTOMER NAME">
                            {{ticketForm.customer_name}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="TYPE OF SERVICE">
                            {{ticketForm.type_service_description}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="3">
                        <el-form-item label="PORT">
                            {{ticketForm.port}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="3">
                        <el-form-item label="DOCUMENT OFFICE">
                            {{ticketForm.type_document_office_description}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="Customer Email 1">
                            {{ticketForm.customer_email_1}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Customer Email 2">
                            <div v-if="ticketForm.customer_email_2">
                                {{ticketForm.customer_email_2}}
                            </div>
                            <div v-else>
                                --
                            </div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="PAYMENT TYPE">
                            {{ticketForm.payment_type_description}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="TICKET CREATED AT">
                            <CreatedAtResult :ticket="ticketForm"></CreatedAtResult>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6" v-if="isAdminOrEmployerUserLogged">
                        <el-form-item label="STATUS">
                            <StatusResult :ticket="ticketForm"></StatusResult>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="COMPLETED DATE AT">
                            <div v-if="ticketForm.completed_date">
                                {{ticketForm.completed_date_format}}
                            </div>
                            <div v-else>
                                --
                            </div>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6" v-if="ticketForm.trucker_id">
                        <el-form-item label="TRUCKER">
                            {{ticketForm.trucker_name}}
                            <router-link plain
                                         :to="{name: 'trucker-to-ticket', params: {trucker_id: ticketForm.trucker_id, ticket_id: ticketForm.id}}">
                                <el-button icon="el-icon-search" circle></el-button>
                            </router-link>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="COMMENTS">
                            <p>{{ticketForm.comments}}</p>
                        </el-form-item>

                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <h3 class="mt-5"><i class="el-icon-download"></i> Attachments
                            ({{ticketForm.attachments.length}})</h3>
                        <hr>
                        <el-table
                                :data="ticketForm.attachments"
                                style="width: 100%">
                            <el-table-column
                                    label="Attachment">
                                <template slot-scope="scope">
                                    <a target="_blank" :href="getUrlToDownloadAttachment(scope.row)">{{scope.row.attachment_name}}</a>
                                </template>
                            </el-table-column>
                            <el-table-column
                                    label="Size">
                                <template slot-scope="scope">
                                    {{scope.row.attachment_size | formatFileSize}}
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-col>
                </el-row>

                <el-row v-if="ticketForm.completed">
                    <el-col class="mb-5">
                        <h3 class="mt-5"><i class="el-icon-message"></i> Message history
                            ({{ticketForm.messages.length}})</h3>
                        <hr>
                        <Messages></Messages>
                    </el-col>
                </el-row>
            </el-form>
        </el-card>

        <message-embedded v-if="!ticketForm.completed"></message-embedded>
    </div>
</template>

<script src="./TicketFormShow.js">

</script>

<style scoped lang="scss">
    /deep/ .el-form-item__label {
        font-weight: bold !important;
        margin-bottom: 0px;
        padding: 0px;
    }

    .comment {
        height: 100px;
    }

    .file-uploads {
        margin-bottom: 0px;
    }
</style>