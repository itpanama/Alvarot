import FileUpload from "vue-upload-component";
import { mapGetters } from "vuex";
const UPLOADING_ATTACHMENTS = "Subiendo archivo(s)...";
let loading;

export default {
  name: "trucker-request-documents",
  props: {
    attachmentsDocuments: {
      type: Array,
      default: function() {
        return [];
      }
    }
  },
  data() {
    const validateAttachments = (rule, value, callback) => {
      if (this.attachments.length === 0) {
        return callback(new Error("Debe seleccionar al menos un documento"));
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

        let message = `El documento no puede tener un tamaño mayor a ${this.$options.filters.formatFileSize(
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
          let message = `Hay documentos duplicados con el mismo nombre, por favor remueva los documentos duplicados.`;

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

    const validateDocumentTypeSelect = (rule, value, callback) => {
      if (this.attachments.length > 0) {
        var invalid = false;
        this.attachments.forEach(attachment => {
          if (!attachment.hasOwnProperty("trucker_document_type_id")) {
            invalid = true;
          }
        });

        if (invalid) {
          let message = `El tipo de documento es requerido.`;

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
      postAaction: `${this.$baseUrl}/api/trucker-request/upload_attachment`,
      errors: [],
      labelPosition: "top",
      attachments: [],
      loading: false,
      messageLoading: "",
      attachmentMaxSize: 1024 * 1024 * 20,
      dataForm: {},
      rules: {
        attachments: [
          { validator: validateAttachments, trigger: "blur" },
          { validator: validateAttachmentsSize, trigger: "blur" },
          { validator: validateDuplicateAttachments, trigger: "blur" },
          { validator: validateDocumentTypeSelect, trigger: "blur" }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      truckersDocumentTypeOptions: "getTruckersDocumentTypeOptions"
    }),
    currentTotalAttachmentsSize() {
      let total = 0;
      this.attachments.map(attachment => {
        total += attachment.size || 0;
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
            attachment.data.attachment_name = response.attachment_name;
            attachment.data.attachment_size = response.attachment_size;
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
              if (loading) {
                loading.close();
              }

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
              this.saveDocumentsToTrucker();
            }
          }
        }
      }
      if (!newFile && oldFile) {
        // remove
        // console.log('remove', oldFile)
      }
    },
    changeTruckerDocumentType(scope) {
      let index = scope.$index;

      this.attachments[index].data.trucker_document_type_id =
        scope.row.trucker_document_type_id;
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
    uploadDocuments() {
      this.$refs.form.validate(valid => {
        if (valid) {
          this.$refs.form.resetFields();

          this.messageLoading = UPLOADING_ATTACHMENTS;

          loading = this.$loading({
            lock: true,
            text: this.messageLoading,
            spinner: "el-icon-loading",
            background: "rgba(0, 0, 0, 0.7)"
          });

          this.$refs.upload.active = true;
        } else {
          this.$notify.error({
            title: "ERROR",
            message:
              "Se ha detectado algunos errores en sus documentos, verifique."
          });
        }
      });
    },
    saveDocumentsToTrucker() {
      let params = {};

      params.attachments = this.attachments.map(attachment => ({
        attachment_name: attachment.data.attachment_name,
        attachment_size: attachment.data.attachment_size,
        trucker_document_type_id: attachment.data.trucker_document_type_id
      }));

      this.messageLoading = "Salvando documentos..";

      loading = this.$loading({
        lock: true,
        text: this.messageLoading,
        spinner: "el-icon-loading",
        background: "rgba(0, 0, 0, 0.7)"
      });

      this.$http
        .post(`${this.$baseUrl}/api/trucker/save-attachments`, params)
        .then(
          response => {
            loading.close();

            this.$notify.success({
              title: "Notification",
              dangerouslyUseHTMLString: true,
              message: "Operation successfully"
            });

            let attachments = response.body.attachments || [];

            attachments.forEach(attachment => {
              let documentType = this.truckersDocumentTypeOptions.find(
                documentType =>
                  Number(documentType.id) ===
                  Number(attachment.trucker_document_type_id)
              );

              attachment.trucker_document_type = documentType.description;

              let attachmentsDocuments = this.attachmentsDocuments;
              attachmentsDocuments.push(attachment);

              this.$emit("update:attachmentsDocuments", attachmentsDocuments);
            });

            this.attachments = [];
          },
          response => {
            loading.close();

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
    selectDocument() {
      this.$refs.upload.$el.querySelector("#file").click();
    }
  },
  mounted() {},
  components: {
    FileUpload
  }
};
