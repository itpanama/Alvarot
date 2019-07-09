<template>
    <el-form v-loading="loading"
             :rules="rules"
             :model="dataForm"
             :element-loading-text="messageLoading"
             element-loading-spinner="el-icon-loading"
             element-loading-background="rgba(0, 0, 0, 0.8)"
             ref="form">

        <el-card class="box-card mt-3">
            <div slot="header" class="clearfix">
                <span>
                    <i class="fa fa-paperclip" aria-hidden="true"></i> ADJUNTAR DOCUMENTOS
                </span>
            </div>

            <el-row :gutter="20">
                <el-col :span="24">
                    <el-form-item prop="attachments">
                        <div class="upload">
                            <div class="example-btn">
                                <file-upload
                                        :drop="true"
                                        class="btn btn-primary"
                                        :post-action="postAaction"
                                        :multiple="true"
                                        :size="attachmentMaxSize"
                                        v-model="attachments"
                                        @input-filter="inputFilter"
                                        @input-file="inputFile"
                                        ref="upload">
                                    <i class="fa fa-plus"></i>
                                    Seleccionar
                                </file-upload>

                                <el-button class="btn btn-success ml-2" :disabled="attachments.length === 0" v-on:click.prevent="uploadDocuments()">
                                    <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                    Subir documento(s)
                                </el-button>
                            </div>

                            <div v-show="attachments.length > 0">
                                <el-table
                                        :data="attachments"
                                        style="width: 100%">
                                    <el-table-column
                                            label="Documento">
                                        <template slot-scope="scope">
                                            {{scope.row.name}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Tipo de documento">
                                        <template slot-scope="scope">
                                            <el-select
                                                    style="width: 100%"
                                                    v-model="scope.row.trucker_document_type_id"
                                                    placeholder=""
                                                    @change="changeTruckerDocumentType(scope, attachments)">
                                                <el-option v-for="item in truckersDocumentTypeOptions" :key="item.id"
                                                           :label="item.description"
                                                           :value="item.id">
                                                </el-option>
                                            </el-select>
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Tamaño">
                                        <template slot-scope="scope">
                                            {{scope.row.size | formatFileSize}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Estatus">
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
                                            label="Acción">
                                        <template slot-scope="scope">
                                            <button class="btn btn-danger"
                                                    @click.prevent="$refs.upload.remove(scope.row);$refs.form.resetFields();">x
                                            </button>
                                        </template>
                                    </el-table-column>
                                </el-table>

                                <div class="mt-5" v-show="attachments.length > 0">
                                    <el-button class="btn btn-primary" v-on:click.prevent="selectDocument()">
                                        <i class="fa fa-plus"></i>
                                        Seleccionar
                                    </el-button>

                                    <el-button class="btn btn-success" :disabled="attachments.length === 0" v-on:click.prevent="uploadDocuments()">
                                        <i class="fa fa-arrow-up" aria-hidden="true"></i>
                                        Subir documento(s)
                                    </el-button>
                                </div>

                                <el-row :gutter="20">
                                    <el-col :span="6">
                                        <strong>Tamaño maximo por documento:</strong> {{attachmentMaxSize | formatFileSize}}
                                    </el-col>
                                    <el-col :span="6">
                                        <strong>Total de documentos adjuntos:</strong> {{attachments.length}}
                                    </el-col>
                                    <el-col :span="6">
                                        <strong>Tamaño total:</strong> {{currentTotalAttachmentsSize | formatFileSize}}
                                    </el-col>
                                </el-row>
                            </div>
                        </div>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-card>

    </el-form>
</template>
<script src="./TruckerRequestDocuments.js">
</script>
<style scoped lang="scss">
.file-uploads {
  margin-bottom: 0px;
}

/deep/ .el-form-item__error {
  font-size: 1em;
  margin-top: 20px;
  margin-bottom: 20px;
  position: relative;
}
</style>
