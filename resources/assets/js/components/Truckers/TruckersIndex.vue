<template>
    <div ref="indexTickets">
        <el-card class="box-card">
            <div slot="header" class="clearfix x">
                <span>TRUCKERS</span>
            </div>
            <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="4">
                        <el-form-item label="ID" prop="ID">
                            <el-input v-model="dataForm.id" placeholder="ID"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Compañía" prop="company_name_operation">
                            <el-input v-model="dataForm.company_name_operation" placeholder="Compañía"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Póliza" prop="number_policy">
                            <el-input v-model="dataForm.number_policy" placeholder="Póliza"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Contacto" prop="contact_name">
                            <el-input v-model="dataForm.contact_name" placeholder="Contacto"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Correo electrónico" prop="email">
                            <el-input v-model="dataForm.email" placeholder="Correo electrónico"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Teléfono" prop="phone">
                            <el-input v-model="dataForm.phone" placeholder="Teléfono"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="4">
                        <el-form-item label="Status" prop="trucker_status_id">
                            <el-select style="width:100%;" clearable v-model="dataForm.trucker_status_id"
                                       placeholder="Status">
                                <el-option v-for="item in truckerStatusOptions" :key="item.id"
                                           :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Fecha de Expiración de Póliza" prop="expiration_date_to_range">
                            <el-date-picker
                                    style="width:100%;"
                                    v-model="dataForm.expiration_date_to_range"
                                    type="daterange"
                                    unlink-panels
                                    range-separator=""
                                    start-placeholder="Fecha Inicio"
                                    end-placeholder="Fecha Fin"
                                    :picker-options="pickerOptions">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Fecha de Creación" prop="created_at_range">
                            <el-date-picker
                                    style="width:100%;"
                                    v-model="dataForm.created_at_range"
                                    type="daterange"
                                    unlink-panels
                                    range-separator=""
                                    start-placeholder="Fecha Inicio"
                                    end-placeholder="Fecha Fin"
                                    :picker-options="pickerOptions">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item>
                    <el-button type="primary" icon="el-icon-search" v-on:click.prevent="submitForm()">FILTRAR</el-button>
                    <el-button v-on:click.prevent="resetForm()">LIMPIAR</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="box-card mt-3">
            <div class="mt-1 mb-1">
                <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
            </div>
            <el-table :data="truckers"
                      style="width: 100%">
                <el-table-column width="50"
                                 prop="id" label="ID">
                </el-table-column>
                <el-table-column prop="company_name_operation" label="Compañía">
                </el-table-column>
                <el-table-column width="180" label="Póliza">
                    <template slot-scope="scope, index">
                        {{scope.row.number_policy}}
                        <div><strong>Expiración:</strong> <span class="text-danger">{{scope.row.expiration_date}}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column prop="contact_name" label="Contacto">
                </el-table-column>
                <el-table-column prop="email" label="Correo electrónico">
                </el-table-column>
                <el-table-column prop="phone" width="120" label="Teléfono">
                </el-table-column>
                <el-table-column label="Fecha de Creación">
                    <template slot-scope="scope, index">
                        {{scope.row.trucker_created_format}}
                    </template>
                </el-table-column>
                <el-table-column label="Status">
                    <template slot-scope="scope, index">
                        {{scope.row.trucker_status_description}}
                    </template>
                </el-table-column>
                <el-table-column label="ACTION" width="150">
                    <template slot-scope="scope, index">
                        <router-link plain
                                     :to="{name: 'trucker-show', params: {trucker_id: scope.row.id}}">
                            <el-button icon="el-icon-search" circle></el-button>
                        </router-link>
                        <router-link plain :to="{name: 'trucker-edit', params: {trucker_id: scope.row.id}}">
                            <el-button type="primary" icon="el-icon-edit" circle></el-button>
                        </router-link>

                        <el-button type="danger" v-if="scope.row.trucker_status_id !== 2" icon="el-icon-delete" circle @click="deleteTrucker(scope.row.id, scope.$index)"></el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="mt-1 mb-1">
                <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
            </div>

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
<script src="./TruckersIndex.js"></script>
<style scoped lang="scss"></style>
