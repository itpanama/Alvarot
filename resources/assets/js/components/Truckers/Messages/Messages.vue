<template>
    <div>
        <el-table
                :data="truckerForm.messages"
                :height="heightTable"
                style="width: 100%">
            <el-table-column type="expand">
                <template slot-scope="props">
                    <h4><i class="el-icon-download"></i> Attachments ({{props.row.attachments.length}})</h4>
                    <el-table
                            :data="props.row.attachments"
                            style="width: 100%">
                        <el-table-column
                                label="Attachment">
                            <template slot-scope="scope">
                                <a target="_blank" :href="getUrlToDownloadAttachment(scope.row)">{{scope.row.attachment_name}}</a>
                            </template>
                        </el-table-column>
                        <el-table-column
                                label="Size">
                            <template slot-scope="scope">
                                {{scope.row.attachment_size | formatFileSize}}
                            </template>
                        </el-table-column>
                    </el-table>
                </template>
            </el-table-column>
            <el-table-column
                    type="index"
                    width="50">
            </el-table-column>
            <el-table-column
                    label="Message">
                <template slot-scope="scope">
                    {{scope.row.comments}}
                </template>
            </el-table-column>
            <el-table-column
                    label="User">
                <template slot-scope="scope">
                    {{scope.row.fullname}} ({{scope.row.rol}})
                </template>
            </el-table-column>
            <el-table-column
                    label="Attachments">
                <template slot-scope="scope">
                    <div v-if="scope.row.attachments.length">
                        {{scope.row.attachments.length}} Attachment(s)
                    </div>
                    <div v-else>
                        --
                    </div>
                </template>
            </el-table-column>
            <el-table-column
                    label="Created at">
                <template slot-scope="scope">
                    {{scope.row.created_at}}
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
  import {mapGetters} from 'vuex';

  export default {
    name: "Messages",
    props:{
      heightTable: {
        type: Number,
        default: 500
      }
    },
    computed: {
      ...mapGetters({
        truckerForm: "getCurrentTrucker"
      }),
    },
    methods: {
      getUrlToDownloadAttachment(attachment) {
        return `${this.$baseUrl}/api/trucker/${attachment.trucker_id}/message_attachment/${attachment.id}/download`;
      },
    }
  }
</script>

<style scoped>

</style>