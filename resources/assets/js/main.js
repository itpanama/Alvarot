import Vue from "./bootstrap";
import App from "./components/App/App.vue";
import ErrorPage from "./components/Errors/ErrorPage.vue";
import router from "./router";
import store from "./vuex/store";
import {UserModel, bus} from './common/config';

let applicationStarted = false;

Vue.http.interceptors.push((request, next) => {
  next(response => {
    if (response.status === 401) {
      if (applicationStarted) {
        router.push({ path: '/pageNotAuthorized'});
      }
    }
  });
});

const startApplication = (response) => {

  const userLogin = response.body;

  UserModel.data = userLogin;

  new Vue({
    components: {
      App
    },
    router,
    store,
    beforeCreate() {
      localStorage.clear();
      localStorage.setItem('user_login', JSON.stringify(userLogin));
      this.$store.commit('setUserLogin', userLogin);
      applicationStarted = true;
    }
  }).$mount('#app');

};

const errorRetrievingCredentials = () => {

  new Vue({
    components: {
      ErrorPage
    },
    template: '<ErrorPage></ErrorPage>',
  }).$mount('#app');

};


// Run application
Vue
  .http
  .get(`${Vue.prototype.$baseUrl}/api/user/my-profile`)
  .then(
    startApplication,
    errorRetrievingCredentials
  );