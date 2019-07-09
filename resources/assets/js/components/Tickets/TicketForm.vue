<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>Ticket</span>
            </div>
            <el-form :rules="rules"
                     :label-position="labelPosition"
                     v-loading="loading"
                     :element-loading-text="messageLoading"
                     element-loading-spinner="el-icon-loading"
                     element-loading-background="rgba(0, 0, 0, 0.8)"
                     ref="form"
                     :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="BL NUMBER" prop="bl_number">
                            <el-input v-model="dataForm.bl_number" placeholder="BL NUMBER"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="CUSTOMER NAME" prop="customer_name">
                            <el-input v-model="dataForm.customer_name" disabled placeholder="CUSTOMER NAME"
                                      :maxlength="20"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="TYPE OF SERVICE" prop="type_service">
                            <el-select
                                    clearable
                                    style="width: 100%;"
                                    v-model="dataForm.type_service"
                                    @change="changeTypeService($event)"
                                    placeholder="TYPE OF SERVICE">
                                <el-option v-for="item in typeServiceOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <!--<el-col :span="4" v-if="showTruckerTypeahead">-->
                        <!--<el-form-item label="TRUCKER" prop="trucker_id">-->
                            <!--<el-select style="width: 100%;"-->
                                       <!--v-model="dataForm.trucker_id"-->
                                       <!--filterable-->
                                       <!--placeholder="TRUCKER"-->
                                       <!--@change="changeTruckerTypeahead($event)">-->
                                <!--<el-option-->
                                        <!--v-for="item in truckersTypeaheadOptions"-->
                                        <!--:key="item.id"-->
                                        <!--:value="item.id"-->
                                        <!--:label="item.typeaheadResult">-->
                                <!--</el-option>-->
                            <!--</el-select>-->
                        <!--</el-form-item>-->
                    <!--</el-col>-->
                    <el-col :span="3">
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

                    <el-col :span="6">
                        <el-form-item label="Customer Email 1" prop="customer_email_1">
                            <el-input v-model="dataForm.customer_email_1" disabled
                                      placeholder="Customer Email 1"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Customer Email 2" prop="customer_email_2">
                            <el-input v-model="dataForm.customer_email_2" disabled
                                      placeholder="Customer Email 2"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="PAYMENT TYPE" prop="payment_type_id">
                            <el-select clearable style="width: 100%;" v-model="dataForm.payment_type_id"
                                       placeholder="PAYMENT TYPE">
                                <el-option v-for="item in paymentTypeOptions" :key="item.id" :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="Comments" prop="comments">
                            <el-input type="textarea" :rows="8" v-model="dataForm.comments"
                                      placeholder="Comments"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="Attachments" prop="attachments">
                            <div class="upload">
                                <div class="example-btn">
                                    <file-upload
                                            :drop="true"
                                            class="btn btn-link"
                                            :post-action="postAaction"
                                            :multiple="true"
                                            :size="attachmentMaxSize"
                                            v-model="attachments"
                                            @input-filter="inputFilter"
                                            @input-file="inputFile"
                                            ref="upload">
                                        <i class="fas fa-paperclip"></i> Add file
                                    </file-upload>
                                </div>

                                <el-table
                                        :data="attachments"
                                        style="width: 100%"
                                        height="250">
                                    <el-table-column
                                            label="Attachment">
                                        <template slot-scope="scope">
                                            {{scope.row.name}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Size">
                                        <template slot-scope="scope">
                                            {{scope.row.size | formatFileSize}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Status">
                                        <template slot-scope="scope">
                        <span v-if="scope.row.error">
                          <i v-bind:title="scope.row.error"
                             class="el-icon-error text-danger"> {{scope.row.error}}
                          </i>
                        </span>
                                            <span v-else-if="scope.row.success">
                          <i class="el-icon-success text-success">
                          </i>
                        </span>
                                            <span v-else-if="scope.row.active">
                          <i class="el-icon-warning text-secondary">
                          </i>
                        </span>
                                            <span v-else>
                          <i class="el-icon-upload"></i>
                        </span>
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Action">
                                        <template slot-scope="scope">
                                            <button class="btn btn-danger"
                                                    @click.prevent="$refs.upload.remove(scope.row)">x
                                            </button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <strong>Max Attachments:</strong> {{maxAttachments}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Max Total Size:</strong> {{attachmentMaxSize | formatFileSize}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Total Attachments:</strong> {{attachments.length}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Total Size:</strong> {{currentTotalAttachmentsSize | formatFileSize}}
                    </el-col>
                </el-row>

                <el-form-item class="mt-5">
                    <el-button type="primary" v-on:click.prevent="submitForm()">SUBMIT</el-button>
                    <el-button v-on:click="resetForm()">RESET</el-button>
                </el-form-item>

                <el-row :gutter="20" class="mt-2 mb-2">
                    <el-col :span="24">
                        (<span class="text-danger">*</span>) Required fields.
                    </el-col>
                </el-row>
            </el-form>
        </el-card>
    </div>
</template>

<script src="./TicketForm.js">

</script>

<style scoped lang="scss">
    .comment {
        height: 100px;
    }

    .file-uploads {
        margin-bottom: 0px;
    }
</style>