export default {
  name: "settings-form",
  data() {
    return {
      dataForm: {
        email_counter_list: "",
        email_trucker_list: "",
      },
      defaultDataForm: {
        email_counter_list: "",
        email_trucker_list: ""
      },
      rules: {
        email_counter_list: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ],
        email_trucker_list: [
          {
            required: false,
            message: "This field is required.",
            trigger: "change"
          }
        ]
      }
    };
  },
  methods: {
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      let loadingInstance = this.$loading();

      this.$http
        .put(`${this.$baseUrl}/api/settings`, params)
        .then(
          response => {
            this.$notify.success({
              title: "Notification",
              dangerouslyUseHTMLString: true,
              message: "Operation successfully"
            });

            this.storeData(response);
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
      if (this.id) {
        this.dataForm = Object.assign({}, {}, this.defaultDataForm);
      } else {
        this.$refs.form.resetFields();
      }
    },
    storeData(response) {
      this.dataForm = response.body;
      this.defaultDataForm = Object.assign({}, {}, this.dataForm);
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/settings`)
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
    }
  },
  mounted() {
    this.fetch();
  }
}