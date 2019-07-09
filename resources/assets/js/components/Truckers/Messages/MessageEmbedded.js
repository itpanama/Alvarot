import Messages from "./Messages";

import FileUpload from "vue-upload-component";
import {mapGetters} from "vuex";
import _ from "lodash";

const dataFormModel = {
  comments: "",
  completed: 0,
  ticket_id: null
};

const UPLOADING_ATTACHMENTS = "Uploading attachment(s)...";
const UPDATING_TICKET = "Updating the ticket, please wait.";

export default {
  name: "MessageEmbedded",
  props: {
    trucker_id: Number
  },
  data() {
    return {
      postAaction: `${this.$baseUrl}/api/trucker/${
        this.trucker_id
        }/messages/upload_attachment`,
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
      truckerForm: "getCurrentTrucker"
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
        newFile.data.trucker_id = this.truckerForm.id;
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

      this.$http
        .post(
          `${this.$baseUrl}/api/trucker/${this.trucker_id}/new-message`,
          params
        )
        .then(
          response => {
            this.loading = false;
            let truckerForm = Object.assign({}, {}, this.truckerForm);
            truckerForm.messages = response.body.trucker.messages;
            this.$store.commit("setCurrentTrucker", truckerForm);

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
    }
  },
  created() {
    // set user logged
    this.userLogin = JSON.parse(localStorage.getItem("user_login"));
  },
  mounted() {
    console.info(this.trucker_id);
  },
  components: {
    FileUpload,
    Messages
  }
};
