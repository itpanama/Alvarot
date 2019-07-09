import {mapGetters} from 'vuex';
import {CONFIG} from "../../common/config";

export default {
  name: "header-navegation",
  data() {
    return {
      urlLogout: `${this.$baseUrl}/logout`,
      token: this.$token,
      activeIndex: "1",
    };
  },
  computed: {
    ...mapGetters([
      'isCustomerUserLogged',
      'isAdminOrEmployerUserLogged',
      'isTruckerUserLogged'
    ]),
    hasPermissionToTruckerModule() {
      return this.isTruckerUserLogged ||
        this.isCustomerUserLogged && this.$store.getters.hasPermission(CONFIG.PERMISSION.TRUCKER_FORM);
    }
  },
  methods: {
    handleSelect(key) {
      if (key === '/my-msc') {
        window.open('https://www.mymsc.com/Link/', '_blank');
        return false;
      }

      this.$router.push({name: key});
    },
    logout() {
      let loadingInstance = this.$loading();
      document.getElementById("logout-form").submit();
      setTimeout(() => {
        loadingInstance.close();
      }, 500);
    }
  },
};
