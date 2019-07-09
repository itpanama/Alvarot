import { mapGetters } from "vuex";

export default {
  name: "trucker-assigned-to-ticket-show",
  props: ["trucker_id", "ticket_id"],
  data() {
    return {
      labelPosition: "top"
    };
  },
  computed: {
    ...mapGetters({
      truckerForm: "getCurrentTrucker"
    })
  },
  methods: {
    getUrlToDownloadAttachment(attachment) {
      return `${this.$baseUrl}/api/truckers/${
        attachment.trucker_id
      }/attachment/${attachment.id}/download`;
    },
    fetch() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/ticket/show/${this.ticket_id}/trucker/${this.trucker_id}`)
        // .get(`${this.$baseUrl}/api/truckers/show/${this.trucker_id}`)
        .then(
          response => {
            this.$store.commit("setCurrentTrucker", response.body.trucker);
          },
          response => {
            //error here
          }
        )
        .finally(() => {
          loadingInstance.close();
        });
    }
  },
  mounted() {
    this.fetch();
  },
  components: {}
};
