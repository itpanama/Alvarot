<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>Trucker ID {{truckerForm.id}}</span>
            </div>
            <el-form :label-position="labelPosition" ref="form">
                <h3><i class="el-icon-tickets"></i> Resumen</h3>
                <hr>
                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="Nombre de la Compañía">
                            {{truckerForm.company_name_operation}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Dirección de la Compañía">
                            {{truckerForm.address_company}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Número de la Póliza">
                            {{truckerForm.number_policy}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Fecha de Vencimiento de la póliza">
                            {{truckerForm.expiration_date}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="Correo electrónico">
                            {{truckerForm.email}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Correo electrónico 2">
                            {{truckerForm.email_2 ? truckerForm.email_2 : '--'}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Teléfono">
                            {{truckerForm.phone}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Teléfono 2">
                            {{truckerForm.phone_2 ? truckerForm.phone_2 : '--'}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <el-form-item label="Nombre del Contacto">
                            {{truckerForm.contact_name}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Status">
                            {{truckerForm.trucker_status_description}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="6">
                        <el-form-item label="Fecha de Creación">
                            {{truckerForm.trucker_created_format}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <h3 class="mt-5"><i class="el-icon-download"></i> Documentos Adjuntos
                            ({{truckerForm.attachments.length}})</h3>
                        <hr>
                        <el-table
                                :data="truckerForm.attachments"
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
                        </el-table>
                    </el-col>
                </el-row>
            </el-form>
        </el-card>

        <message-embedded :trucker_id="trucker_id">
        </message-embedded>
    </div>
</template>
<script src="./TruckerShow.js"></script>
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
