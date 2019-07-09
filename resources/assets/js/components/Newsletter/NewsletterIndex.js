import { mapGetters } from "vuex";
import moment from "moment";

const defaultData = {
  id: "",
  title: "",
  expiration_date_to_range: "",
  created_at_range: ""
};

export default {
  name: "newsletter-index",
  data() {
    return {
      labelPosition: "top",
      dialogVisible: false,
      dataForm: { ...defaultData },
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
      newsletter: "getAllNewsletter"
    })
  },
  methods: {
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
      this.dataForm = { ...defaultData };
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
        .get(`${this.$baseUrl}/api/newsletter`, { params })
        .then(
          response => {
            loadingInstance.close();
            this.$store.commit("refreshNewsletter", response.body.data);
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
    editTicket(id) {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/ticket/show/${id}`)
        .then(
          response => {
            this.$store.commit("setCurrentTicket", response.body.ticket);
            this.dialogVisible = true;
          },
          response => {
            //error here
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    },
    deleteNewsletter(id, index) {
      this.$confirm("Are you sure to delete this newsletter?")
        .then(sure => {
          if (sure) {
            const url = `${this.$baseUrl}/api/newsletter/delete/${id}`;

            let loadingInstance = this.$loading();
            this.$http
              .delete(url, {})
              .then(
                response => {
                  if (response && response.body === true) {
                    this.newsletter.splice(index, 1);
                    let message = "Newsletter deleted successfully";
                    this.$message.success(message);
                  } else {
                    this.$message.error(response.body);
                  }
                },
                () => {
                  this.$message.error("Error deleting newsletter");
                }
              )
              .finally(() => {
                loadingInstance.close();
              });
          }
        })
        .catch(() => {});
    },
    avoidDragAndDrog(avoidDragAndDrop = true) {
      let fnAvoidDragAndDrog = e => {
        e.preventDefault();
      };

      if (this.$refs.indexTickets) {
        if (avoidDragAndDrop) {
          this.$refs.indexTickets.addEventListener(
            "dragover",
            fnAvoidDragAndDrog
          );
          this.$refs.indexTickets.addEventListener("drop", fnAvoidDragAndDrog);
        } else {
          this.$refs.indexTickets.removeEventListener(
            "dragover",
            fnAvoidDragAndDrog
          );
          this.$refs.indexTickets.removeEventListener(
            "drop",
            fnAvoidDragAndDrog
          );
        }
      }
    }
  },
  mounted() {
    this.avoidDragAndDrog();

    this.$store.dispatch("fetchTruckerStatusOptions");

    this.fetch();
  },
  components: {},
  beforeDestroy() {
    this.avoidDragAndDrog(false);
  }
};
