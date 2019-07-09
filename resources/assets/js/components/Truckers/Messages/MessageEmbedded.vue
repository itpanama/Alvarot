<template>
    <div class="mt-2">
        <el-row>
            <el-col :span="10">
                <el-card class="box-card">
                    <div slot="header" class="clearfix">
                        <h3><i class="el-icon-tickets"></i> New Message</h3>
                    </div>

                    <el-form
                            :rules="rules"
                            :label-position="labelPosition"
                            ref="form"
                            :model="dataForm"
                            v-loading="loading"
                            :element-loading-text="messageLoading"
                            element-loading-spinner="el-icon-loading"
                            element-loading-background="rgba(0, 0, 0, 0.8)">
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
                                <div class="example-simple">
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
                                </div>
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
                            <el-button type="primary" v-on:click.prevent="submitForm()" :disabled="loading">SEND MESSAGE</el-button>
                            <el-button v-on:click="resetForm()">RESET</el-button>
                        </el-form-item>

                        <el-row :gutter="20" class="mt-2 mb-2">
                            <el-col :span="24">
                                (<span class="text-danger">*</span>) Required fields.
                            </el-col>
                        </el-row>
                    </el-form>

                </el-card>
            </el-col>
            <el-col :span="14">
                <el-card class="box-card ml-2">
                    <div slot="header" class="clearfix">
                        <h3><i class="el-icon-message"></i> Message history ({{truckerForm.messages.length}})</h3>
                    </div>
                    <Messages></Messages>
                </el-card>
            </el-col>
        </el-row>
    </div>
</template>
<script src="./MessageEmbedded.js"></script>
<style scoped lang="scss">
    /deep/ .el-card__header{
        background: #5f686e;
        color: white;
    }
</style>
