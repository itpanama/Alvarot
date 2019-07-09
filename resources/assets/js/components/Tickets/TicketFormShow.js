import StatusResult from "./Columns/StatusResult";
import CreatedAtResult from "./Columns/CreatedAtResult";
import Messages from "./Messages/Messages";
import MessageEmbedded from "./Messages/MessageEmbedded.vue";
import { mapGetters } from "vuex";

export default {
  name: "ticket-form-show",
  data() {
    return {
      labelPosition: "top"
    };
  },
  computed: {
    ...mapGetters({
      ticketForm: "getCurrentTicket",
      isAdminOrEmployerUserLogged: "isAdminOrEmployerUserLogged"
    })
  },
  methods: {
    getUrlToDownloadAttachment(attachment) {
      return `${this.$baseUrl}/api/ticket/${attachment.ticket_id}/attachment/${
        attachment.id
      }/download`;
    },
    fetch() {
      let ticket_id = this.$route.params.ticket_id;

      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/ticket/show/${ticket_id}`)
        .then(
          response => {
            this.$store.commit("setCurrentTicket", response.body.ticket);
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
  components: {
    StatusResult,
    CreatedAtResult,
    Messages,
    MessageEmbedded
  }
};
