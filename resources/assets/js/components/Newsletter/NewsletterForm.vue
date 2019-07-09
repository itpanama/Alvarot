<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>NEWSLETTER</span>
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
                    <el-col :span="8">
                        <el-form-item label="TITLE" prop="title">
                            <el-input v-model="dataForm.title" placeholder="TITLE"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="RANK OF DATES (Start date)" prop="start_date">
                            <el-date-picker
                                    style="width: 100%"
                                    v-model="dataForm.start_date"
                                    type="date"
                                    format="yyyy-MM-dd"
                                    placeholder="Start date">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="RANK OF DATES (End date)" prop="end_date">
                            <el-date-picker
                                    style="width: 100%"
                                    v-model="dataForm.end_date"
                                    type="date"
                                    format="yyyy-MM-dd"
                                    placeholder="Start date">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20" v-if="dataForm.id">
                    <el-col :span="20">
                        <iframe v-bind:src="getUrlToDownloadAttachment('inline')" width="100%" height="350px">
                        </iframe>
                        <strong>Download:</strong> <a target="_blank" :href="getUrlToDownloadAttachment()">{{dataForm.attachment_name}}</a>
                        <div>
                            <strong>Total Size:</strong> {{dataForm.attachment_size | formatFileSize}}
                        </div>
                    </el-col>
                </el-row>

                <el-row :gutter="20" class="mt-5" v-if="!dataForm.id">
                    <el-col :span="24">
                        <el-form-item label="Attachment" prop="attachment">
                            <div class="upload">
                                <div class="example-btn">
                                    <file-upload
                                            :drop="true"
                                            class="btn btn-link"
                                            :post-action="postAaction"
                                            :multiple="false"
                                            :size="attachmentMaxSize"
                                            v-model="attachments"
                                            :extensions="extensions"
                                            :accept="accept"
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

                                <el-row :gutter="20">
                                    <el-col :span="6">
                                        <strong>Max Total Size:</strong> {{attachmentMaxSize | formatFileSize}}
                                    </el-col>
                                    <el-col :span="6">
                                        <strong>Total Size:</strong> {{currentTotalAttachmentsSize | formatFileSize}}
                                    </el-col>
                                </el-row>
                            </div>
                        </el-form-item>
                    </el-col>
                </el-row>



                <el-form-item class="mt-5">
                    <el-button type="primary" v-on:click.prevent="submitForm()">SUBMIT</el-button>
                    <el-button v-on:click="resetForm">RESET</el-button>
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
<script src="./NewsletterForm.js">
</script>
<style scoped lang="scss">
    .comment {
        height: 100px;
    }

    .file-uploads {
        margin-bottom: 0px;
    }
</style>
