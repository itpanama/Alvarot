import HeaderNavegation from './HeaderNavegation.vue';
import {mapGetters} from 'vuex';

export default {
  name: "app",
  data() {
    return {
      activeIndex: "1",
    };
  },
  computed: {
    ...mapGetters({
      userLogin: 'getUserLogin'
    }),
  },
  methods: {
    handleSelect() {
    },
    fetchUser() {
      const loadingInstance = this.$loading();
      this.$http.get(`${this.$baseUrl}/api/user/my-profile`).then(
        (response) => {
          if (response && response.body) {
            let userLogin = response.body;
            localStorage.clear();
            localStorage.setItem('user_login', JSON.stringify(userLogin));
            this.$store.commit('setUserLogin', userLogin);
          }

          loadingInstance.close();
        },
        response => {
          loadingInstance.close();
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
      );
    }
  },
  mounted() {
  },
  components: {
    HeaderNavegation
  }
};
