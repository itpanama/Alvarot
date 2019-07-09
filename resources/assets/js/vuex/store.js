import Vue from "vue";
import Vuex from "vuex";
import { CONFIG } from "../common/config";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    completedOptions: [{ value: 1, label: "Yes" }, { value: 0, label: "No" }],
    containerSiteTypeOptions: [
      { id: "20", description: "20" },
      { id: "40", description: "40" }
    ],
    tickets: [],
    truckers: [],
    truckersOptions: [],
    truckersDocumentTypeOptions: [],
    newsletter: [],
    currentTicket: {
      attachments: [],
      messages: []
    },
    currentTrucker: {
      attachments: [],
      messages: []
    },
    userLogin: {
      role_id: null,
      permissions: []
    },
    paymentTypeOptions: [],
    truckerStatusOptions: [],
    typePortOptions: [],
    typeServiceOptions: [],
    typeDocumentOfficeOptions: [],
    employerOptions: []
  },
  getters: {
    hasPermission: (state) => (permission_id) => {
      return state.userLogin.permissions.includes(permission_id);
    },
    isAdminOrEmployerUserLogged(state) {
      return (
        [CONFIG.ROL.EMPLOYER, CONFIG.ROL.ADMIN].indexOf(
          state.userLogin.role_id
        ) !== -1
      );
    },
    getTruckersTypeaheadOptions(state) {
      return state.truckersOptions || [];
    },
    getTruckersDocumentTypeOptions(state) {
      return state.truckersDocumentTypeOptions || [];
    },
    isEmployerUserLogged(state) {
      return state.userLogin.role_id === CONFIG.ROL.EMPLOYER;
    },
    isCustomerUserLogged(state) {
      return state.userLogin.role_id === CONFIG.ROL.CUSTOMER;
    },
    isAdminUserLogged(state) {
      return state.userLogin.role_id === CONFIG.ROL.ADMIN;
    },
    isTruckerUserLogged(state) {
      return state.userLogin.role_id === CONFIG.ROL.TRUCKER;
    },
    getUserLogin(state) {
      return state.userLogin;
    },
    getPaymentTypeOptions(state) {
      return state.paymentTypeOptions;
    },
    getTypePortOptions(state) {
      return state.typePortOptions;
    },
    getCompletedOptions(state) {
      return state.completedOptions;
    },
    getTruckerStatusOptions(state) {
      return state.truckerStatusOptions;
    },
    getTypeServiceOptions(state) {
      return state.typeServiceOptions;
    },
    getCurrentTicket(state) {
      return state.currentTicket;
    },
    getCurrentTrucker(state) {
      return state.currentTrucker;
    },
    getAllTickets(state) {
      return state.tickets || [];
    },
    getAllTruckers(state) {
      return state.truckers || [];
    },
    getAllNewsletter(state) {
      return state.newsletter || [];
    },
    getEmployerOptions(state) {
      return state.employerOptions || [];
    },
    getTypeDocumentOfficeOptions(state) {
      return state.typeDocumentOfficeOptions || [];
    },
    getContainerSiteTypeOptions(state) {
      return state.containerSiteTypeOptions || [];
    }
  },
  mutations: {
    setCurrentTicket(state, currentTicket) {
      state.currentTicket = currentTicket;
    },
    truckersDocumentTypeOptions(state, documentTypes) {
      state.truckersDocumentTypeOptions = documentTypes;
    },
    setCurrentTrucker(state, currentTrucker) {
      state.currentTrucker = currentTrucker;
    },
    setUserLogin(state, userLogin) {
      state.userLogin = userLogin;
    },
    refreshTickets(state, tickets) {
      state.tickets = tickets;
    },
    refreshNewsletter(state, newsletter) {
      state.newsletter = newsletter;
    },
    refreshTruckers(state, truckers) {
      state.truckers = truckers;
    },
    updateTicket(state, ticket) {
      for (let index = 0; index < state.tickets.length; index += 1) {
        if (state.tickets[index].id === ticket.id) {
          state.tickets.splice(index, 1, ticket);
        }
      }
    },
    updatePaymentTypeOptions(state, options) {
      state.paymentTypeOptions = options;
    },
    updateTruckerTypeaheadOptions(state, truckers) {
      state.truckersOptions = truckers;
    },
    updateTruckerStatusOptions(state, options) {
      state.truckerStatusOptions = options;
    },
    updateTypeServiceOptions(state, options) {
      state.typeServiceOptions = options;
    },
    updateTypePortOptions(state, options) {
      state.typePortOptions = options;
    },
    updateEmployerOptions(state, options) {
      state.employerOptions = options;
    },
    updateTypeDocumentOfficeOptions(state, options) {
      state.typeDocumentOfficeOptions = options;
    }
  },
  actions: {
    fetchOptions({ commit, state }) {
      Vue.http
        .get(`${Vue.prototype.$baseUrl}/api/general/options`)
        .then(response => {
          commit("updatePaymentTypeOptions", response.body.payment_types || []);
          commit("updateTypeServiceOptions", response.body.type_services || []);
          commit("updateTypePortOptions", response.body.type_ports || []);
          commit(
            "updateTypeDocumentOfficeOptions",
            response.body.type_document_office || []
          );

          if (state.userLogin.role_id !== CONFIG.ROL.CUSTOMER) {
            commit("updateEmployerOptions", response.body.employers || []);
          }
        });
    },
    fetchTruckerStatusOptions({ commit }) {
      Vue.http
        .get(`${Vue.prototype.$baseUrl}/api/general/options_trucker_status`)
        .then(response => {
          commit(
            "updateTruckerStatusOptions",
            response.body.trucker_status || []
          );
        });
    },
    fetchTruckersOptions({ commit }) {
      Vue.http
        .get(`${Vue.prototype.$baseUrl}/api/truckers/active`)
        .then(response => {
          commit("updateTruckerTypeaheadOptions", response.body || []);
        });
    }
  }
});
