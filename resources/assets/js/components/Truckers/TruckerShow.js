import { mapGetters } from "vuex";
import MessageEmbedded from "./Messages/MessageEmbedded.vue";

export default {
  name: "trucker-show",
  data() {
    return {
      labelPosition: "top",
      trucker_id: Number(this.$route.params.trucker_id)
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
        .get(`${this.$baseUrl}/api/truckers/show/${this.trucker_id}`)
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
  components: {
    MessageEmbedded
  }
};
