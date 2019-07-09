import FileUpload from "vue-upload-component";
import { mapGetters } from "vuex";
import { CONFIG } from "../../common/config";

const UPLOADING_ATTACHMENTS = "Uploading attachment(s)...";
const CREATING_TICKET = "Creating ticket, please wait.";

export default {
  name: "ticket-form",
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
      postAaction: `${this.$baseUrl}/api/ticket/upload_attachment`,
      maxAttachments: 5,
      errors: [],
      labelPosition: "top",
      attachments: [],
      attachmentMaxSize: 1024 * 1024 * 20,
      userLogin: {},
      loading: false,
      messageLoading: "",
      dataForm: {
        bl_number: "",
        customer_name: "",
        type_service: "",
        customer_email_1: "",
        customer_email_2: "",
        type_port_id: "",
        type_document_office_id: "",
        comments: "",
        trucker_id: ""
      },
      rules: {
        bl_number: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        customer_name: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        payment_type_id: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        type_port_id: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        type_document_office_id: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        type_service: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        payment_type: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          }
        ],
        // trucker_id: [
        //   {
        //     required: false,
        //     message: "This field is required.",
        //     trigger: "change"
        //   }
        // ],
        customer_email_1: [
          {
            required: true,
            message: "This field is required.",
            trigger: "change"
          },
          {
            type: "email",
            message: "Invalid email.",
            trigger: "change"
          }
        ],
        customer_email_2: [
          {
            type: "email",
            message: "Invalid email.",
            trigger: "change"
          }
        ],
        comments: [
          { required: true, message: "This field is required", trigger: "blur" }
        ],
        attachments: [
          { validator: validateAttachments, trigger: "blur" },
          { validator: validateAttachmentsSize, trigger: "blur" },
          { validator: validateDuplicateAttachments, trigger: "blur" }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      typeServiceOptions: "getTypeServiceOptions",
      truckersTypeaheadOptions: "getTruckersTypeaheadOptions",
      paymentTypeOptions: "getPaymentTypeOptions",
      typeDocumentOfficeOptions: "getTypeDocumentOfficeOptions",
      typePortOptions: "getTypePortOptions"
    }),
    currentTotalAttachmentsSize() {
      let total = 0;
      this.attachments.map(attachment => {
        total += attachment.size;
      });

      return total;
    },
    showTruckerTypeahead() {
      return [
        CONFIG.TYPE_SERVICES.RELEASE,
        CONFIG.TYPE_SERVICES.EMPTY_RETURN
      ].includes(this.dataForm.type_service);
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
      this.$refs.form.validate(valid => {
        if (valid) {
          if (
            this.attachments.filter(atachment => atachment.active === false)
          ) {
            this.loading = true;
            this.messageLoading = UPLOADING_ATTACHMENTS;
            this.$refs.upload.active = true;
          } else {
            this.messageLoading = CREATING_TICKET;
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
    },
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      params.attachments = this.attachments.map(attachment => {
        let data = Object.assign({}, {}, attachment.data);
        delete data._token;
        return data;
      });

      this.loading = true;
      this.messageLoading = CREATING_TICKET;

      this.$http.post(`${this.$baseUrl}/api/ticket/new`, params).then(
        response => {
          this.loading = false;

          this.$router.push({
            name: "my-tickets"
          });

          this.$notify.success({
            title: "Notification",
            dangerouslyUseHTMLString: true,
            message: "Operation successfully <br>Ticket ID: " + response.body.id
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
    },
    changeTruckerTypeahead(trucker_id) {
      console.info(trucker_id);
    },
    changeTypeService(type_service) {
      if (
        [
          CONFIG.TYPE_SERVICES.RELEASE,
          CONFIG.TYPE_SERVICES.EMPTY_RETURN
        ].includes(type_service)
      ) {
        // _.extend(this.rules, {
        //   trucker_id: [
        //     {
        //       required: true,
        //       message: "This field is required.",
        //       trigger: "blur"
        //     }
        //   ]
        // });
      } else {
        delete this.rules.trucker_id;
      }

      this.dataForm.trucker_id = null;
    }
  },
  created() {
    // set user logged
    this.userLogin = JSON.parse(localStorage.getItem("user_login"));
    this.dataForm.customer_name = this.userLogin.name;
    this.dataForm.customer_email_1 = this.userLogin.email;
    this.dataForm.customer_email_2 = this.userLogin.customer.email_optional;
  },
  mounted() {
    this.$store.dispatch("fetchOptions");
    this.$store.dispatch("fetchTruckersOptions");
  },
  components: {
    FileUpload
  }
};
