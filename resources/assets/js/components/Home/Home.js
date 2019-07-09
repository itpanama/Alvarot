import {mapGetters} from 'vuex';

export default {
  name: "Home",
  data() {
    return {
      newsletters: []
    };
  },
  computed: {
    ...mapGetters([
      'isTruckerUserLogged',
    ]),
  },
  methods: {
    newsletterUrl(newsletter) {
      return `${this.$baseUrl}/api/newsletter/${newsletter.id}/attachment-inline`;
    },
    fetchNewsletter() {
      let loadingInstance = this.$loading();

      this.$http
        .get(`${this.$baseUrl}/api/newsletter/active`)
        .then(
          response => {
            loadingInstance.close();
            this.newsletters = response.body;
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
    if (!this.isTruckerUserLogged) {
      this.fetchNewsletter();
    }
  }
};
