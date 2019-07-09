<template>
    <div>
        <el-form :rules="rules" ref="form" :model="dataForm">
            <el-card class="box-card">
                <div slot="header" class="clearfix">
                    <span>SETTINGS</span>
                </div>

                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="E-mail Counter" prop="email_counter_list">
                            <el-input type="textarea" v-model="dataForm.email_counter_list"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="E-mail Trucker" prop="email_trucker_list">
                            <el-input type="textarea" v-model="dataForm.email_trucker_list"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
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

    </div>
</template>

<script src="./SettingsForm.js">

</script>

<style scoped>

</style>