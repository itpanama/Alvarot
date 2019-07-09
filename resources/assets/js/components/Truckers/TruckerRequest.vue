<template>
    <div>
        <el-tabs type="border-card">
            <el-tab-pane label="DATOS DEL TRANSPORTISTA">
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
                            <el-form-item label="Nombre de la Compañía" prop="company_name_operation">
                                <el-input v-model="dataForm.company_name_operation"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Dirección de la Compañía" prop="address_company">
                                <el-input v-model="dataForm.address_company"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Número de la Póliza" prop="number_policy">
                                <el-input v-model="dataForm.number_policy"></el-input>
                                <div class="text-info line-height-20">
                                    Nota: Para multiples pólizas separar con comas Ejemplo: 41-42,44-42,42-21
                                </div>
                            </el-form-item>

                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Fecha de Vencimiento de la póliza" prop="expiration_date">
                                <el-date-picker
                                        style="width: 100%"
                                        v-model="dataForm.expiration_date"
                                        type="date"
                                        format="yyyy-MM-dd">
                                </el-date-picker>
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :span="6">
                            <el-form-item label="Correo electrónico" prop="email">
                                <el-input v-model="dataForm.email"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Correo electrónico 2" prop="email_2">
                                <el-input v-model="dataForm.email_2"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Teléfono" prop="phone">
                                <el-input v-model="dataForm.phone"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6">
                            <el-form-item label="Teléfono 2" prop="phone_2">
                                <el-input v-model="dataForm.phone_2"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :span="6">
                            <el-form-item label="Nombre del Contacto" prop="contact_name">
                                <el-input v-model="dataForm.contact_name"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="6" v-if="dataForm.trucker_status_description">
                            <el-form-item label="Status" prop="trucker_status_description">
                                {{dataForm.trucker_status_description}}
                            </el-form-item>
                        </el-col>
                        <el-col :span="6" v-if="dataForm.trucker_created_format">
                            <el-form-item label="Fecha de Creación">
                                {{dataForm.trucker_created_format}}
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-form-item class="mt-4">
                        <el-button type="primary" v-on:click.prevent="submitForm()">GUARDAR</el-button>
                        <el-button v-on:click="resetForm()">LIMPIAR</el-button>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
        </el-tabs>

        <trucker-request-documents
                v-if="dataForm.id"
                :attachments-documents.sync="dataForm.attachments">
        </trucker-request-documents>

        <el-card v-if="dataForm.id" class="box-card mt-3">
            <div slot="header" class="clearfix">
                <span><i class="el-icon-download"></i> DOCUMENTOS ({{dataForm.attachments.length}})</span>
            </div>

            <div>
                <el-table
                        :data="dataForm.attachments"
                        style="width: 100%">
                    <el-table-column
                            label="Documento">
                        <template slot-scope="scope">
                            <a target="_blank" :href="getUrlToDownloadAttachment(scope.row)">{{scope.row.attachment_name}}</a>
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="Tipo de documento">
                        <template slot-scope="scope">
                            {{scope.row.trucker_document_type}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="Tamaño">
                        <template slot-scope="scope">
                            {{scope.row.attachment_size | formatFileSize}}
                        </template>
                    </el-table-column>
                    <el-table-column
                            label="Acción">
                        <template slot-scope="scope">
                            <button class="btn btn-danger"
                                    @click.prevent="removeAttachment(scope.row.id, scope.$index)">x
                            </button>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-card>

        <message-embedded
                v-if="dataForm.id"
                :trucker_id="dataForm.id">
        </message-embedded>
    </div>
</template>
<script src="./TruckerRequest.js">
</script>
<style scoped lang="scss">
/deep/ .el-form-item__label {
  font-weight: bold !important;
  margin-bottom: 0px;
  padding: 0px;
}
.line-height-20{
    line-height: 20px;
}
.comment {
  height: 100px;
}

.file-uploads {
  margin-bottom: 0px;
}
</style>
