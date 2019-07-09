import StatusResult from "./Columns/StatusResult";
import CreatedAtResult from "./Columns/CreatedAtResult";
import { mapGetters } from "vuex";

export default {
  name: "my-tickets-index",
  data() {
    return {
      tickets: [],
      labelPosition: "top",
      employers: [],
      dataForm: {
        ticket_id: "",
        bl_number: "",
        type_service_id: ""
      },
      last_page: 0,
      limitByPage: 0
    };
  },
  computed: {
    ...mapGetters({
      typeServiceOptions: "getTypeServiceOptions"
    })
  },
  methods: {
    submitForm() {
      this.dataForm.page = null;
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
        .get(`${this.$baseUrl}/api/tickets`, { params })
        .then(
          response => {
            loadingInstance.close();
            this.tickets = response.body.data;
            this.employers = response.body.employers;
            this.last_page = response.body.last_page;
            this.limitByPage = response.body.per_page;
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
    deleteTicket(id, index) {
      this.$confirm("Are you sure to delete this ticket?")
        .then(sure => {
          if (sure) {
            const url = `${this.$baseUrl}/api/ticket/delete/${id}`;

            let loadingInstance = this.$loading();
            this.$http
              .delete(url, {})
              .then(
                response => {
                  if (response && response.body === true) {
                    this.tickets.splice(index, 1);
                    let message = "Ticket deleted successfully";
                    this.$message.success(message);
                  } else {
                    this.$message.error(response.body);
                  }
                },
                () => {
                  this.$message.error("Error deleting ticket");
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
    this.$store.dispatch("fetchOptions");
    this.fetch();
  },
  components: {
    StatusResult,
    CreatedAtResult
  }
};
