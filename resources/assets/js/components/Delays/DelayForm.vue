<template>
    <div>
        <el-card class="box-card">
            <div slot="header" class="clearfix x">
                <span>EMPTY RETURN CALCULATIONS</span>
            </div>
            <el-form :rules="rules" @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form"
                     :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="CNR OR BL NUMBER" prop="bl_number">
                            <el-input v-model="dataForm.bl_number" placeholder="CNR OR BL NUMBER"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="DISCHARGE DATE" prop="discharge_date">
                            <el-date-picker
                                    style="width: 100%;"
                                    v-model="dataForm.discharge_date"
                                    type="date"
                                    placeholder="DISCHARGE DATE">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="PORT" prop="port">
                            <el-input v-model="dataForm.port" placeholder="PORT"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="CONTAINER SIZE TYPE" prop="container_size_type">
                            <el-select clearable style="width: 100%;" v-model="dataForm.container_size_type"
                                       placeholder="CONTAINER SIZE TYPE">
                                <el-option v-for="item in containerSiteTypeOptions" :key="item.id"
                                           :label="item.description"
                                           :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item
                                style="width: 100%;"
                                label="FREE DAYS DEMURRAGE"
                                prop="free_days_demurrage">
                            <el-input
                                    :readonly="true"
                                    v-model="dataForm.free_days_demurrage"
                                      placeholder="FREE DAYS DEMURRAGE">
                            </el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="RETURN DATE" prop="return_date">
                            <el-date-picker
                                    style="width: 100%;"
                                    v-model="dataForm.return_date"
                                    type="date"
                                    placeholder="RETURN DATE">
                            </el-date-picker>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="DEMURRAGE AMOUNT TO BE PAID">
                            {{dataForm.demurrage_amount_to_be_paid}}
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item class="mt-4">
                    <el-button type="primary" disabled v-on:click.prevent="submitForm()">SUBMIT</el-button>
                    <el-button v-on:click="resetForm()">RESET</el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>
<script src="./DelayForm.js"></script>
<style scoped lang="scss"></style>
