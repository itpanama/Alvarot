import {mapGetters} from "vuex";
import moment from "moment";
import TruckerRequestDocuments from "./TruckerRequestDocuments.vue";
import MessageEmbedded from "./Messages/MessageEmbedded.vue";

export default {
  name: "trucker-request",
  data() {
    return {
      labelPosition: "top",
      loading: false,
      messageLoading: "",
      dataForm: {
        company_name_operation: "",
        address_company: "",
        number_policy: "",
        expiration_date: "",
        email: "",
        email_2: "",
        phone: "",
        phone_2: "",
        attachments: []
      },
      defaultDataForm: {},
      rules: {
        company_name_operation: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        address_company: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        expiration_date: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        number_policy: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        contact_name: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        phone: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "blur"
          }
        ],
        email: [
          {
            required: true,
            message: "Este campo es requerido.",
            trigger: "change"
          },
          {
            type: "email",
            message: "Correo electrónico inválido.",
            trigger: "change"
          }
        ],
        email_2: [
          {
            type: "email",
            message: "Correo electrónico inválido.",
            trigger: "change"
          }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      truckerStatusOptions: "getTruckerStatusOptions",
      truckersDocumentTypeOptions: "getTruckersDocumentTypeOptions"
    })
  },
  methods: {
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      params.expiration_date = moment(params.expiration_date).format(
        "YYYY-MM-DD"
      );

      let loadingInstance = this.$loading();

      this.$http
        .put(`${this.$baseUrl}/api/trucker-request`, params)
        .then(
          response => {
            let truckerRequest = response.body.trucker;
            truckerRequest.attachments = truckerRequest.attachments || [];

            this.storeData(truckerRequest);

            this.$store.commit(
              "truckersDocumentTypeOptions",
              truckerRequest.trucker_document_type || []
            );

            this.$store.commit("setCurrentTrucker", truckerRequest);

            this.$notify.success({
              title: "Notification",
              dangerouslyUseHTMLString: true,
              message: "Operación realizada exitosamente."
            });
          },
          response => {
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
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    submitForm() {
      this.$refs.form.validate(valid => {
        if (valid) {
          this._save();
        } else {
          this.$notify.error({
            title: "ERROR",
            message: "Formulario invalido."
          });
          return false;
        }
      });
    },
    resetForm() {
      this.dataForm = Object.assign({}, {}, this.defaultDataForm);
    },
    getUrlToDownloadAttachment(attachment) {
      let trucker_id = this.dataForm.id;
      return `${this.$baseUrl}/api/truckers/${trucker_id}/attachment/${
        attachment.id
        }/download`;
    },
    storeData(trucker) {
      let data = _.cloneDeep(trucker);
      this.dataForm = data;

      if (data.expiration_date) {
        this.dataForm.expiration_date = moment(
          data.expiration_date,
          "YYYY-MM-DD"
        ).toDate();
      }

      this.defaultDataForm = Object.assign({}, {}, this.dataForm);
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/trucker-request`)
        .then(
          response => {
            let truckerRequest = response.body.trucker;
            truckerRequest.attachments = truckerRequest.attachments || [];

            this.storeData(truckerRequest);

            this.$store.commit(
              "truckersDocumentTypeOptions",
              truckerRequest.trucker_document_type || []
            );

            this.$store.commit("setCurrentTrucker", truckerRequest);
          },
          response => {
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
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    removeAttachment(id, index) {
      this.$confirm("¿Está seguro que desea eliminar este documento?")
        .then(sure => {
          if (sure) {
            const url = `${this.$baseUrl}/api/trucker/delete-attachment/${id}`;

            let loadingInstance = this.$loading();
            this.$http
              .delete(url, {})
              .then(
                response => {
                  if (response && response.body === true) {
                    this.dataForm.attachments.splice(index, 1);
                    let message = "Documento eliminado existosamente";
                    this.$message.success(message);
                  } else {
                    this.$message.error(response.body);
                  }
                },
                () => {
                  this.$message.error(
                    "Ha ocurrido un error eliminado este documento."
                  );
                }
              )
              .finally(() => {
                loadingInstance.close();
              });
          }
        })
        .catch(_ => {
        });
    }
  },
  mounted() {
    this.fetch();
  },
  components: {
    TruckerRequestDocuments,
    MessageEmbedded
  }
};
