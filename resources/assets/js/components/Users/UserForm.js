export default {
  name: "user-form",
  props: ["id"],
  data() {
    return {
      labelPosition: "top",
      roles: [],
      dataForm: {
        name: "",
        email: "",
        username: "",
        password: "",
        password_confirmation: "",
        active: true,
        role_id: null,
      },
      defaultDataForm: {},
      rules: {
        name: [
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
        username: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          },
          {
            validator: (rule, value, callback) => {
              let alphanum = /^[0-9A-Z]*$/i;

              if (alphanum.test(value)) {
                callback();
              } else {
                return callback(new Error('The username field may only contain alpha-numeric characters.'));
              }

            }, trigger: 'change'
          }
        ],
        role_id: [
          {
            required: true,
            message: "This field is required.",
            trigger: "blur"
          }
        ]
      }
    };
  },
  methods: {
    _save() {
      let params = Object.assign({}, {}, this.dataForm);

      let loadingInstance = this.$loading();

      if (this.id) {
        this.$http
          .put(`${this.$baseUrl}/api/users/${this.id}`, params)
          .then(
            response => {

              this.$router.push({
                name: "users"
              });

              this.$notify.success({
                title: "Notification",
                dangerouslyUseHTMLString: true,
                message: "Operation successfully"
              });

              // this.storeData(response);
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
      } else {
        this.$http
          .post(`${this.$baseUrl}/api/users/new`, params)
          .then(
            response => {
              this.$notify.success({
                title: "Notification",
                dangerouslyUseHTMLString: true,
                message: "Operation successfully"
              });

              this.$router.push({
                name: "user-edit",
                params: {
                  id: response.body.id
                }
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
      }
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
      this.dataForm.active = this.dataForm.active === 1 ? true : false;
      this.defaultDataForm = Object.assign({}, {}, this.dataForm);
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/users/${this.id}`)
        .then(
          response => {
            this.roles = response.body.roles;
            this.storeData(response);
          },
          response => {
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    fetchRoles() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/users/roles`)
        .then(
          response => {
            this.roles = response.body;
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
    if (this.id) {
      this.fetch();
    } else {
      this.fetchRoles();
    }
  }
};
