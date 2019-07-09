import { mapGetters } from "vuex";
import moment from "moment";
import { CONFIG } from "../../common/config";
import MessageEmbedded from "./Messages/MessageEmbedded.vue";

export default {
  name: "trucker-form",
  props: ["trucker_id"],
  data() {
    return {
      labelPosition: "top",
      loading: false,
      messageLoading: "",
      dataForm: {
        username: "",
        password: "",
        password_confirmation: "",
        active: false,
        attachments: []
      },
      defaultDataForm: {},
      rules: {
        company_name_operation: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        address_company: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        expiration_date: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        number_policy: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        contact_name: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        phone: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        email: [
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
        email_2: [
          {
            type: "email",
            message: "Invalid email.",
            trigger: "change"
          }
        ],
        trucker_status_id: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        username: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      truckerStatusOptions: "getTruckerStatusOptions"
    })
  },
  methods: {
    changeTruckerStatus(trucker_status_id) {
      if (
        trucker_status_id === CONFIG.TRUCKER_STATUS.APROBADO &&
        !this.dataForm.user_id
      ) {
        this.$set(this.dataForm, "active", true);
      }
    },
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      params.expiration_date = moment(params.expiration_date).format(
        "YYYY-MM-DD"
      );

      let loadingInstance = this.$loading();

      this.$http
        .put(`${this.$baseUrl}/api/truckers/${this.trucker_id}`, params)
        .then(
          response => {
            this.$store.commit("setCurrentTrucker", response.body.trucker);

            this.$notify.success({
              title: "Notification",
              dangerouslyUseHTMLString: true,
              message: "Operation successfully"
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
            message: "Invalid form."
          });
          return false;
        }
      });
    },
    resetForm() {
      if (this.trucker_id) {
        this.dataForm = Object.assign({}, {}, this.defaultDataForm);
      } else {
        this.$refs.form.resetFields();
      }
    },
    getUrlToDownloadAttachment(attachment) {
      return `${this.$baseUrl}/api/truckers/${
        attachment.trucker_id
      }/attachment/${attachment.id}/download`;
    },
    storeData(trucker) {
      let data = _.cloneDeep(trucker);
      this.dataForm = data;
      this.dataForm.active = data.active === 1 ? true : false;

      this.dataForm.expiration_date = moment(
        data.expiration_date,
        "YYYY-MM-DD"
      ).toDate();

      this.defaultDataForm = Object.assign({}, {}, this.dataForm);
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/truckers/show/${this.trucker_id}`)
        .then(
          response => {
            let trucker = response.body.trucker;
            this.storeData(trucker);
            this.$store.commit("setCurrentTrucker", trucker);
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
    }
  },
  mounted() {
    this.$store.dispatch("fetchTruckerStatusOptions");

    this.fetch();
  },
  components: {
    MessageEmbedded
  }
};
