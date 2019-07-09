<template>
    <div>
        <el-form :rules="rules"
                 :label-position="labelPosition"
                 v-loading="loading"
                 :element-loading-text="messageLoading"
                 element-loading-spinner="el-icon-loading"
                 element-loading-background="rgba(0, 0, 0, 0.8)"
                 ref="form"
                 :model="dataForm">

            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>TRUCKER</span>
                </div>
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
                                    format="yyyy-MM-dd"
                                    placeholder="Start date">
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
                    <el-col :span="6">
                        <el-form-item label="Status" prop="trucker_status_description">
                            <el-select
                                    style="width: 100%"
                                    v-model="dataForm.trucker_status_id"
                                    placeholder=""
                                    @change="changeTruckerStatus($event)">
                                <el-option v-for="item in truckerStatusOptions" :key="item.id"
                                           :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>

                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Fecha de Creación">
                            {{dataForm.trucker_created_format}}
                        </el-form-item>
                    </el-col>
                </el-row>

            </el-card>


            <el-card class="box-card mt-3">
                <div slot="header" class="clearfix">
                    <span>Login Credentials</span>
                </div>
                <div class="item">
                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="USERNAME" prop="username">
                                <el-input v-model="dataForm.username" placeholder="USERNAME"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="STATUS" prop="active">
                                <el-checkbox v-model="dataForm.active">Active</el-checkbox>
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row :gutter="20">
                        <el-col :span="8">
                            <el-form-item label="PASSWORD" prop="password">
                                <el-input type="password" v-model="dataForm.password" placeholder="PASSWORD"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="8">
                            <el-form-item label="CONFIRM PASSWORD" prop="password_confirmation">
                                <el-input type="password" v-model="dataForm.password_confirmation"
                                          placeholder="PASSWORD CONFIRMATION"></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </div>
            </el-card>

            <el-card class="box-card mt-3">
                <div class="item">
                    <el-form-item class="mt-4">
                        <el-button type="primary" v-on:click.prevent="submitForm()">SUBMIT</el-button>
                        <el-button v-on:click="resetForm()">RESET</el-button>
                    </el-form-item>
                </div>
            </el-card>

        </el-form>

        <el-card class="box-card mt-3">
            <div slot="header" class="clearfix">
                <span>Documentos Adjuntos</span>
            </div>
            <el-table
                    :data="dataForm.attachments"
                    style="width: 100%">
                <el-table-column
                        label="Documento">
                    <template slot-scope="scope">
                        <a target="_blank"
                           :href="getUrlToDownloadAttachment(scope.row)">{{scope.row.attachment_name}}</a>
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
            </el-table>
        </el-card>

        <message-embedded :trucker_id="dataForm.id">
        </message-embedded>
    </div>
</template>
<script src="./TruckerForm.js">
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
