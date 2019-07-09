export default {
  name: "customers-index",
  data() {
    return {
      labelPosition: "top",
      dataForm: {
        name: "",
        email: "",
        username: "",
        page: null
      },
      customers: [],
      last_page: 0,
      limitByPage: 0,
      totalRecords: 0
    };
  },
  methods: {
    submitForm() {
      this.fetch();
    },
    resetForm() {
      this.$refs.form.resetFields();
      this.dataForm.page = null;
      this.fetch();
    },
    handleCurrentChange(page) {
      this.dataForm.page = page;
      this.fetch();
    },
    fetch() {
      let params = Object.assign({}, {}, this.dataForm);
      let loadingInstance = this.$loading();
      this.$http
        .get(`${this.$baseUrl}/api/customers`, { params })
        .then(
          response => {
            loadingInstance.close();
            this.customers = response.body.data;
            this.last_page = response.body.last_page;
            this.limitByPage = response.body.per_page;
            this.totalRecords = response.body.total;
          },
          () => {
            this.$notify.error({
              title: "ERROR",
              dangerouslyUseHTMLString: true,
              message: "Error"
            });
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    deleteUser(id, index) {
      this.$confirm("Are you sure to delete this customer?")
        .then(sure => {
          if (sure) {
            const url = `${this.$baseUrl}/api/customers/delete/${id}`;

            let loadingInstance = this.$loading();
            this.$http
              .delete(url, {})
              .then(
                response => {
                  if (response && response.body === true) {
                    this.customers.splice(index, 1);
                    let message = "Customer deleted successfully";
                    this.$message.success(message);
                  } else {
                    this.$message.error(response.body);
                  }
                },
                error => {
                  this.$message.error("Error deleting customer");
                }
              )
              .finally(() => {
                loadingInstance.close();
              });
          }
        })
        .catch(() => {});
    }
  },
  mounted() {
    this.fetch();
  }
};
