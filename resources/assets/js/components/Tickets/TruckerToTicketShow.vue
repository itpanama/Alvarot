<template>
    <div>
        <el-card class="box-card">
            <el-breadcrumb separator="/">
                <el-breadcrumb-item :to="{name: 'ticket-show', params: {ticket_id: this.$route.params.ticket_id}}">Ticket {{$route.params.ticket_id}}</el-breadcrumb-item>
                <el-breadcrumb-item>Trucker {{truckerForm.id}}</el-breadcrumb-item>
            </el-breadcrumb>
        </el-card>

        <el-card class="box-card mt-3">
            <el-form :label-position="labelPosition" ref="form">
                <h3><i class="el-icon-tickets"></i> Trucker Summary</h3>
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
            </el-form>
        </el-card>
    </div>
</template>
<script src="./TruckerToTicketShow.js"></script>
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
