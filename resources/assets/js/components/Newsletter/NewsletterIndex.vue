<template>
    <div ref="indexNewsletter">
        <el-card class="box-card">
            <div slot="header" class="clearfix">
                <span>NEWSLETTER</span>
            </div>
            <el-form @keyup.enter.native="submitForm()" :label-position="labelPosition" ref="form" :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="2">
                        <el-form-item label="ID" prop="ID">
                            <el-input v-model="dataForm.id" placeholder="ID"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="TITLE" prop="title">
                            <el-input v-model="dataForm.title" placeholder="TITLE"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item>
                    <el-button type="primary" icon="el-icon-search" v-on:click.prevent="submitForm()">FILTER</el-button>
                    <el-button v-on:click.prevent="resetForm()">RESET</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="box-card mt-3">
            <div slot="header" class="clearfix">
                <div class="float-left">
                    <span class="font-weight-bold">Total Records:</span> <span>{{totalRecords}}</span>
                </div>

                <router-link class="float-right" :to="{name: 'newsletter-new'}">
                    <i class="el-icon-circle-plus"></i> Newsletter
                </router-link>
            </div>

            <el-table :data="newsletter"
                      style="width: 100%">
                <el-table-column width="50"
                                 prop="id" label="ID">
                </el-table-column>
                <el-table-column prop="title" label="Title">
                </el-table-column>
                <el-table-column prop="start_date" label="START DATE">
                </el-table-column>
                <el-table-column prop="end_date" label="END DATE">
                </el-table-column>
                <el-table-column label="ACTION" width="150">
                    <template slot-scope="scope, index">
                        <router-link plain :to="{name: 'newsletter-edit', params: {id: scope.row.id}}">
                            <el-button type="primary" icon="el-icon-edit" circle></el-button>
                        </router-link>
                        <el-button type="danger" icon="el-icon-delete" circle @click="deleteNewsletter(scope.row.id, scope.$index)"></el-button>
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
<script src="./NewsletterIndex.js">
</script>
<style scoped lang="scss">
</style>
