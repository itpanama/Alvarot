import FileUpload from "vue-upload-component";
import moment from "moment";

const UPLOADING_ATTACHMENTS = "Uploading attachment(s)...";
const CREATE_OR_UPDATE = "Updating newsletter, please wait.";

export default {
  name: "newsletter-form",
  props: ["id"],
  data() {
    const validateAttachments = (rule, value, callback) => {
      if (this.attachments.length === 0) {
        return callback(new Error("You must attach at least one file"));
      } else {
        callback();
      }
    };

    const validateAttachmentsSize = (rule, value, callback) => {
      if (this.attachments.length > 0) {
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

        let message = `The max size of attachments cannot be more than ${this.$options.filters.formatFileSize(
          this.attachmentMaxSize
        )}`;

        if (attachmentsTotalSize > this.attachmentMaxSize) {
          this.$notify.error({
            title: "ERROR",
            message: message
          });

          return callback(new Error(message));
        } else {
          callback();
        }
      } else {
        callback();
      }
    };

    const validateDuplicateAttachments = (rule, value, callback) => {
      if (this.attachments.length > 0) {
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

          return callback(new Error(message));
        } else {
          callback();
        }
      } else {
        callback();
      }
    };

    return {
      postAaction: `${this.$baseUrl}/api/newsletter/upload_attachment`,
      maxAttachments: 1,
      errors: [],
      labelPosition: "top",
      attachments: [],
      attachmentMaxSize: 1024 * 1024 * 5,
      loading: false,
      messageLoading: "",
      accept: 'application/pdf',
      extensions: 'pdf',
      dataForm: {
        title: "",
        created_at_range: ""
      },
      defaultDataForm: {},
      rules: {
        title: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        start_date: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        end_date: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        attachment: [
          {validator: validateAttachments, trigger: "blur"},
          {validator: validateAttachmentsSize, trigger: "blur"},
          {validator: validateDuplicateAttachments, trigger: "blur"}
        ]
      },
      pickerOptions: {
        shortcuts: [{
          text: 'Last week',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: 'Last month',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: 'Last 3 months',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          }
        }]
      },
    };
  },
  computed: {
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
              this.loading = false;

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
    submitForm() {
      if (this.id) {
        // edit
        this.$refs.form.validate(valid => {
          if (valid) {
            this._save();
          } else {
            this.$notify.error({
              title: "ERROR",
              message:
                "There were problems trying to submit the form, make sure you have all the required fields filled out."
            });
            return false;
          }
        });
      } else {
        // new
        this.$refs.form.validate(valid => {
          if (valid) {
            if (
              this.attachments.filter(atachment => atachment.active === false)
            ) {
              this.loading = true;
              this.messageLoading = UPLOADING_ATTACHMENTS;
              this.$refs.upload.active = true;
            } else {
              this.messageLoading = CREATE_OR_UPDATE;
              this._save();
            }
          } else {
            this.$notify.error({
              title: "ERROR",
              message:
                "There were problems trying to submit the form, make sure you have all the required fields filled out."
            });
            return false;
          }
        });
      }
    },
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      if (this.attachments.length) {
        let attachment = _.first(this.attachments);
        let dataAttachment = Object.assign({}, {}, attachment.data);
        delete dataAttachment._token;

        params.attachment = dataAttachment;
      }

      let date1Value = moment(params.start_date).format('YYYY-MM-DD');
      params.start_date = date1Value;

      let date2Value = moment(params.end_date).format('YYYY-MM-DD');
      params.end_date = date2Value;

      delete params.created_at_range;

      this.loading = true;
      this.messageLoading = CREATE_OR_UPDATE;

      let url = `${this.$baseUrl}/api/newsletter`;
      if (!this.dataForm.id) {
        url += `/new`;
      } else {
        url += `/edit/${this.dataForm.id}`;
      }

      this.$http.post(url, params).then(
        response => {
          this.loading = false;

          this.$router.push({
            name: "newsletter"
          });

          this.$notify.success({
            title: "Notification",
            dangerouslyUseHTMLString: true,
            message: "Operation successfully <br>Newsletter ID: " + response.body.id
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
      if (this.$refs.upload) {
        this.$refs.upload.clear();
      }

      if (this.id) {
        let data = _.cloneDeep(this.defaultDataForm);
        this.dataForm = data;
      } else {
        this.$refs.form.resetFields();
      }
    },
    storeData(response) {
      let data = _.cloneDeep(response.body);
      this.dataForm = data;

      this.dataForm.start_date = moment(data.start_date, 'YYYY-MM-DD').toDate();
      this.dataForm.end_date = moment(data.end_date, 'YYYY-MM-DD').toDate();

      this.defaultDataForm = Object.assign({}, {}, this.dataForm);
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/newsletter/${this.id}`)
        .then(
          response => {
            this.storeData(response);
          },
          response => {
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    getUrlToDownloadAttachment(type = 'download') { // download | inline
      return `${this.$baseUrl}/api/newsletter/${this.dataForm.id}/attachment-${type}`;
    },
  },
  mounted() {
    if (this.id) {
      this.fetch();
    }
  },
  components: {
    FileUpload
  }
};
