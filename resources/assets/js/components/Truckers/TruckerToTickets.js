import { mapGetters } from "vuex";

export default {
  name: "trucker-to-tickets",
  data() {
    return {
      tickets: [],
      labelPosition: "top",
      employers: [],
      dataForm: {
        ticket_id: "",
        bl_number: ""
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
        .get(`${this.$baseUrl}/api/tickets/assigned-to-trucker`, { params })
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
    }
  },
  mounted() {
    this.fetch();
  }
};
