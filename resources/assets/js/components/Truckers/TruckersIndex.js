import {mapGetters} from "vuex";
import moment from "moment";

const defaultData = {
  id: "",
  company_name_operation: "",
  number_policy: "",
  contact_name: "",
  email: "",
  phone: "",
  trucker_status_id: "",
  expiration_date_to_range: "",
  created_at_range: ""
};

export default {
  name: "truckers-index",
  data() {
    return {
      labelPosition: "top",
      dialogVisible: false,
      dataForm: {...defaultData},
      last_page: 0,
      limitByPage: 0,
      totalRecords: 0,
      pickerOptions: {
        shortcuts: [
          {
            text: "Last week",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "Last month",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit("pick", [start, end]);
            }
          },
          {
            text: "Last 3 months",
            onClick(picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit("pick", [start, end]);
            }
          }
        ]
      }
    };
  },
  computed: {
    ...mapGetters({
      truckers: "getAllTruckers",
      truckerStatusOptions: "getTruckerStatusOptions"
    })
  },
  methods: {
    deleteTrucker(id, index) {
      this.$confirm("Are you sure to delete this trucker?")
        .then(sure => {
          if (sure) {
            const url = `${this.$baseUrl}/api/trucker/delete/${id}`;

            let loadingInstance = this.$loading();
            this.$http
              .delete(url, {})
              .then(
                response => {
                  if (response && response.body === true) {
                    this.truckers.splice(index, 1);
                    let message = "Trucker deleted successfully";
                    this.$message.success(message);
                  } else {
                    this.$message.error(response.body);
                  }
                },
                error => {
                  this.$message.error(error.body.error);
                }
              )
              .finally(() => {
                loadingInstance.close();
              });
          }
        })
        .catch(() => {
        });
    },
    changeUnassignedTickets() {
      this.dataForm.user_id_assigned = "";
    },
    changeTruckerStatus(scope) {
      let loadingInstance = this.$loading();

      let params = {
        id: scope.row.id,
        trucker_status_id: scope.row.trucker_status_id
      };

      this.$http
        .post(`${this.$baseUrl}/api/truckers/change_status`, params)
        .then(
          () => {
            loadingInstance.close();
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
    changeUser(scope) {
      let loadingInstance = this.$loading();

      let params = {
        ticket_id: scope.row.id,
        user_id_assigned: scope.row.user_id_assigned
      };

      this.$http
        .post(`${this.$baseUrl}/api/tickets/change_assignation`, params)
        .then(
          () => {
            loadingInstance.close();
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
    handleCurrentChange(page) {
      this.dataForm.page = page;
      this.fetch();
    },
    submitForm() {
      this.dataForm.page = null;
      this.fetch();
    },
    resetForm() {
      this.$refs.form.resetFields();
      this.dataForm = {...defaultData};
      this.fetch();
    },
    fetch() {
      let params = Object.assign({}, {}, this.dataForm);

      delete params.created_at_range;
      delete params.expiration_date_to_range;

      params.created_at_start = "";
      params.created_at_end = "";

      if (this.dataForm.created_at_range) {
        params.created_at_start = moment(
          this.dataForm.created_at_range[0]
        ).format("YYYY-MM-DD");
        params.created_at_end = moment(
          this.dataForm.created_at_range[1]
        ).format("YYYY-MM-DD");
      }

      params.expiration_date_at_start = "";
      params.expiration_date_at_end = "";

      if (this.dataForm.expiration_date_to_range) {
        params.expiration_date_at_start = moment(
          this.dataForm.expiration_date_to_range[0]
        ).format("YYYY-MM-DD");
        params.expiration_date_at_end = moment(
          this.dataForm.expiration_date_to_range[1]
        ).format("YYYY-MM-DD");
      }

      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/truckers`, {params})
        .then(
          response => {
            loadingInstance.close();
            this.$store.commit("refreshTruckers", response.body.data);
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
    }
  },
  mounted() {
    this.$store.dispatch("fetchTruckerStatusOptions");

    this.fetch();
  },
  components: {}
};
