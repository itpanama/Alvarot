<template>
    <div>
        <el-dialog
                title="New Message"
                width="80%"
                :visible.sync="dialogVisible"
                v-loading.fullscreen.lock="loading"
                :element-loading-text="messageLoading"
                element-loading-spinner="el-icon-loading"
                element-loading-background="rgba(0, 0, 0, 0.8)"
                :before-close="beforeClose">

            <el-form :rules="rules" :label-position="labelPosition" ref="form" :model="dataForm">
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="BL NUMBER" prop="bl_number">
                            {{ticketForm.bl_number}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="CUSTOMER NAME" prop="customer_name">
                            {{ticketForm.customer_name}}
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="TYPE OF SERVICE" prop="type_service">
                            {{ticketForm.type_service_description}}
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="TICKET CREATED AT" prop="create_at">
                            <CreatedAtResult :ticket="ticketForm"></CreatedAtResult>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="STATUS" prop="status">
                            <StatusResult :ticket="ticketForm"></StatusResult>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="COMPLETED" prop="completed">
                            <el-select
                                    v-model="dataForm.completed"
                                    placeholder=""
                                    @change="changeCompleted">
                                <el-option v-for="item in completedOptions" :key="item.value" :label="item.label"
                                           :value="item.value">
                                </el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>

                <hr>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <el-form-item label="Comments" prop="comments">
                            <el-input type="textarea" :rows="8" v-model="dataForm.comments"
                                      placeholder="Comments"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="24">
                        <div class="example-simple">
                            <div class="upload">
                                <div class="example-btn">
                                    <file-upload
                                            :drop="true"
                                            class="btn btn-link"
                                            :post-action="postAaction"
                                            :multiple="true"
                                            :size="attachmentMaxSize"
                                            v-model="attachments"
                                            @input-filter="inputFilter"
                                            @input-file="inputFile"
                                            ref="upload">
                                        <i class="fas fa-paperclip"></i> Add file
                                    </file-upload>
                                </div>

                                <el-table
                                        :data="attachments"
                                        style="width: 100%"
                                        height="250">
                                    <el-table-column
                                            label="Attachment">
                                        <template slot-scope="scope">
                                            {{scope.row.name}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Size">
                                        <template slot-scope="scope">
                                            {{scope.row.size | formatFileSize}}
                                        </template>
                                    </el-table-column>
                                    <el-table-column
                                            label="Status">
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
                                            label="Action">
                                        <template slot-scope="scope">
                                            <button class="btn btn-danger"
                                                    @click.prevent="$refs.upload.remove(scope.row)">x
                                            </button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </div>
                        </div>
                    </el-col>
                </el-row>

                <el-row :gutter="20">
                    <el-col :span="6">
                        <strong>Max Attachments:</strong> {{maxAttachments}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Max Total Size:</strong> {{attachmentMaxSize | formatFileSize}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Total Attachments:</strong> {{attachments.length}}
                    </el-col>
                    <el-col :span="6">
                        <strong>Total Size:</strong> {{currentTotalAttachmentsSize | formatFileSize}}
                    </el-col>
                </el-row>

                <el-row :gutter="20" class="mt-2 mb-2">
                    <el-col :span="24">
                        (<span class="text-danger">*</span>) Required fields.
                    </el-col>
                </el-row>
            </el-form>

            <div class="mt-2">
                <h3 class="mt-5"><i class="el-icon-message"></i> Message history ({{ticketForm.messages.length}})</h3>
                <hr>
                <Messages :heightTable="200"></Messages>
            </div>

            <div slot="footer" class="dialog-footer">
                <el-button v-on:click="resetForm()">Cancel</el-button>
                <el-button type="primary" @click="submitForm()">Send</el-button>
            </div>
        </el-dialog>
    </div>
</template>

<script>
  import StatusResult from "../Columns/StatusResult";
  import CreatedAtResult from "../Columns/CreatedAtResult";
  import Messages from "./Messages";
  import FileUpload from "vue-upload-component";
  import {mapGetters} from 'vuex';
  import _ from 'lodash';

  const dataFormModel = {
    comments: "",
    completed: 0,
    ticket_id: null,
  };

  const UPLOADING_ATTACHMENTS = "Uploading attachment(s)...";
  const UPDATING_TICKET = "Updating the ticket, please wait.";

  export default {
    name: "dialog-message",
    props: ["dialogVisible"],
    data() {
      return {
        postAaction: `${this.$baseUrl}/api/messages/upload_attachment`,
        maxAttachments: 5,
        errors: [],
        labelPosition: "top",
        attachments: [],
        attachmentMaxSize: 1024 * 1024 * 20,
        userLogin: {},
        loading: false,
        messageLoading: "",
        dataForm: {...dataFormModel},
        rules: {
          comments: [
            {
              required: true,
              message: "This field is required",
              trigger: "blur"
            }
          ]
        }
      };
    },
    computed: {
      ...mapGetters({
        ticketForm: 'getCurrentTicket',
        completedOptions: 'getCompletedOptions',
      }),
      currentTotalAttachmentsSize() {
        let total = 0;
        this.attachments.map(attachment => {
          total += attachment.size;
        });

        return total;
      }
    },
    methods: {
      changeCompleted() {
        console.info(this.dataForm);
      },
      beforeClose() {
        // this.$confirm('Are you sure to close this dialog?')
        //   .then(() => {
        //     this.$emit('update:dialogVisible', false);
        //   })
        //   .catch(() => {
        //   });

        this.$emit('update:dialogVisible', false);
      },
      inputFilter(newFile, oldFile, prevent) {
        if (newFile && !oldFile) {
          if (/(\/|^)(Thumbs\.db|desktop\.ini|\..+)$/.test(newFile.name)) {
            return prevent();
          }

          if (/\.(php5?|html?|jsx?)$/i.test(newFile.name)) {
            return prevent();
          }
        }
      },
      inputFile(newFile, oldFile) {
        if (newFile && !oldFile) {
          // add
          newFile.data.ticket_id = this.ticketForm.id;
          newFile.data._token = document
            .querySelector("[name=csrf-token]")
            .getAttribute("content");
        }
        if (newFile && oldFile) {
          // update
          if (newFile.success) {
            let response = JSON.parse(newFile.xhr.responseText);

            if (response.attachment_name) {
              let attachment = this.$refs.upload.get(newFile.id);
              attachment.data = response;
            }
          }

          // with all process finish
          if (newFile.xhr && newFile.xhr.status && newFile.active === false) {
            if (this.$refs.upload.uploaded) {
              this.$refs.upload.active = false;
              this.refreshStateAttachments();

              let attachmentsError = this.attachments.filter(
                document => document.xhr && document.xhr.status !== 200
              );
              if (attachmentsError && attachmentsError.length) {
                let message = `Unable to upload ${
                  attachmentsError.length
                  } file(s). <br>`;

                message += `<br><strong>Error details:</strong> <br>`;
                attachmentsError.map(document => {
                  let response = JSON.parse(document.xhr.response);
                  message += `-  ${response.error}<br>`;
                });

                this.$notify.error({
                  title: "Attachment Process",
                  dangerouslyUseHTMLString: true,
                  message: message
                });
                this.loading = false;
              } else {
                this._save();
              }
            }
          }
        }
        if (!newFile && oldFile) {
          // remove
          // console.log('remove', oldFile)
        }
      },
      refreshStateAttachments() {
        this.attachments.map(attachment => {
          if (attachment.error !== "") {
            attachment.error = "";
            attachment.active = false;
          }
          attachment.speed = "0.00";
        });
      },
      submitForm(forceSubmit = null) {
        if (
          forceSubmit === null &&
          this.dataForm.comments.search(/adjunto|attached/i) !== -1 &&
          this.attachments.length === 0
        ) {
          const regex = /(attached|adjunto)/gi;
          const str = this.dataForm.comments;
          let m, word;

          while ((m = regex.exec(str)) !== null) {
            // This is necessary to avoid infinite loops with zero-width matches
            if (m.index === regex.lastIndex) {
              regex.lastIndex++;
            }

            // The result can be accessed through the `m`-variable.
            m.forEach(match => {
              word = match;
            });
          }

          let message = `You wrote "${word}" in your message, but there are no files attached. Send anyway?`;

          this.$confirm(message)
            .then(sure => {
              if (sure) {
                this.submitForm(true);
              }
            })
            .catch(() => {
            });

          return;
        }

        this.$refs.form.validate(valid => {
          if (valid) {
            if (this.attachments.length) {
              let attachmentsTotalSize = 0;
              this.attachments.map(attachment => {
                attachmentsTotalSize += attachment.size;
                if (attachment.error) {
                  this.$refs.upload.update(attachment, {
                    active: false,
                    error: ""
                  });
                }
              });

              if (attachmentsTotalSize > this.attachmentMaxSize) {
                this.$notify.error({
                  title: "ERROR",
                  message: `The max size of attachments cannot be more than ${this.$options.filters.formatFileSize(
                    this.attachmentMaxSize
                  )}`
                });
                return;
              }

              if (this.attachments.length > this.maxAttachments) {
                this.$notify.error({
                  title: "",
                  dangerouslyUseHTMLString: true,
                  message: `The maximum limit of attachments should not be greater than ${
                    this.maxAttachments
                    }.`
                });
                return;
              }

              // check duplicate attachment name
              let names = [];
              this.attachments.map(attachment => {
                names.push(attachment.name);
              });

              if (_.uniq(names).length !== names.length) {
                let message = `There are duplicate attachments, please remove any duplicate before continue.`;

                this.$notify.error({
                  title: "ERROR",
                  message: message
                });

                return;
              }

              this.loading = true;
              this.messageLoading = UPLOADING_ATTACHMENTS;
              this.$refs.upload.active = true;
            } else {
              this._save();
            }
          } else {
            this.$notify.error({
              title: "ERROR",
              message: "Invalid form."
            });
            return false;
          }
        });
      },
      _save() {
        let params = Object.assign({}, {}, this.dataForm);

        params.attachments = this.attachments.map(attachment => {
          let data = Object.assign({}, {}, attachment.data);
          delete data._token;
          return data;
        });

        this.loading = true;
        this.messageLoading = UPDATING_TICKET;

        this.$http.post(`${this.$baseUrl}/api/messages/new`, params).then(
          (response) => {
            this.loading = false;

            let ticket = response.body.ticket;

            this.$store.commit('updateTicket', _.cloneDeep(ticket));

            this.$notify.success({
              title: "Notification",
              dangerouslyUseHTMLString: true,
              message: "Operation successfully"
            });

            this.resetForm();
          },
          response => {
            this.loading = false;
            let messageHtml = "<ul>";

            let errors = 0;

            for (var field in response.body.errors) {
              let errorList = response.body.errors[field];
              errorList.map(error => {
                errors++;
                messageHtml += `<li>${error}</li>`;
              });
            }

            messageHtml += "<ul>";

            let message = `<strong>Error Details (${errors})</strong><br>${messageHtml}`;

            this.$notify.error({
              title: "ERROR",
              dangerouslyUseHTMLString: true,
              message: message
            });
          }
        );
      },
      resetForm() {
        this.$refs.upload.clear();
        this.$refs.form.resetFields();
        this.$emit('update:dialogVisible', false);
      },
    },
    created() {
      // set user logged
      this.userLogin = JSON.parse(localStorage.getItem('user_login'));
    },
    components: {
      FileUpload,
      StatusResult,
      CreatedAtResult,
      Messages
    },
    watch: {
      dialogVisible: function (value) {
        if (value) {
          this.dataForm.completed = this.ticketForm.completed;
          this.dataForm.ticket_id = this.ticketForm.id;
        } else {
          this.dataForm = {...dataFormModel};
        }
      }
    }
  };
</script>

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