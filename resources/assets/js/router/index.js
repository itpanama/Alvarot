import Vue from "vue";
import Router from "vue-router";
import Home from "../components/Home/Home.vue";
import UsersIndex from "../components/Users/UsersIndex.vue";
import UserForm from "../components/Users/UserForm.vue";

import TicketForm from "../components/Tickets/TicketForm.vue";
import TicketFormShow from "../components/Tickets/TicketFormShow.vue";
import MyTicketsIndex from "../components/Tickets/MyTicketsIndex.vue";
import TicketsIndex from "../components/Tickets/TicketsIndex.vue";
import TruckersIndex from "../components/Truckers/TruckersIndex.vue";
import TruckerShow from "../components/Truckers/TruckerShow.vue";
import TruckerToTickets from "../components/Truckers/TruckerToTickets.vue";
import TruckerToTicketShow from "../components/Tickets/TruckerToTicketShow.vue";
import TruckerForm from "../components/Truckers/TruckerForm.vue";
import TruckerRequest from "../components/Truckers/TruckerRequest.vue";

import CustomersIndex from "../components/Customers/CustomersIndex.vue";
import CustomerForm from "../components/Customers/CustomerForm.vue";

import StatusIndex from "../components/Administration/Status/StatusIndex.vue";
import StatusForm from "../components/Administration/Status/StatusForm.vue";
import ReportTickets from "../components/Reports/ReportTickets.vue";
import DelayForm from "../components/Delays/DelayForm.vue";
import CalculateReleaseForm from "../components/Delays/CalculateReleaseForm.vue";
import MonitorForm from "../components/Administration/Monitor/MonitorForm.vue";
import SettingsForm from "../components/Administration/Settings/SettingsForm.vue";

import NewsletterIndex from "../components/Newsletter/NewsletterIndex.vue";
import NewsletterForm from "../components/Newsletter/NewsletterForm.vue";


import Page404 from "../components/Errors/Page404.vue";
import pageNotAuthorized from "../components/Errors/pageNotAuthorized.vue";
import { CONFIG, UserModel } from "../common/config";
import _ from "lodash";

Vue.use(Router);

const routes = [
  {
    path: "/",
    name: "landingpage",
    component: Home
  },
  {
    path: "/page404",
    name: "page404",
    component: Page404
  },
  {
    path: "/pageNotAuthorized",
    name: "pageNotAuthorized",
    component: pageNotAuthorized
  },
  {
    path: "/users",
    name: "users",
    component: UsersIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/users/new",
    name: "user-new",
    component: UserForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/users/edit/:id",
    name: "user-edit",
    component: UserForm,
    props: true,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/customers",
    name: "customers",
    component: CustomersIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/customers/new",
    name: "customer-new",
    component: CustomerForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/customers/edit/:id",
    name: "customer-edit",
    component: CustomerForm,
    props: true,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/ticket-new",
    name: "ticket-new",
    component: TicketForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.CUSTOMER]
    }
  },
  {
    path: "/tickets/show/:ticket_id",
    name: "ticket-show",
    component: TicketFormShow
  },
  {
    path: "/tickets",
    name: "tickets",
    component: TicketsIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/newsletter",
    name: "newsletter",
    component: NewsletterIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/newsletter/edit/:id",
    name: "newsletter-edit",
    props: true,
    component: NewsletterForm
  },
  {
    path: "/newsletter/new",
    name: "newsletter-new",
    component: NewsletterForm
  },
  {
    path: "/truckers",
    name: "truckers",
    component: TruckersIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/truckers/edit/:trucker_id",
    name: "trucker-edit",
    component: TruckerForm,
    props: true,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/truckers/show/:trucker_id",
    name: "trucker-show",
    component: TruckerShow
  },
  {
    path: "/tickets/show/:ticket_id/trucker/:trucker_id",
    name: "trucker-to-ticket",
    props: true,
    component: TruckerToTicketShow
  },
  {
    path: "/trucker-request",
    name: "trucker-request",
    component: TruckerRequest,
    meta: {
      rolUserAllowed: [CONFIG.ROL.TRUCKER, CONFIG.ROL.CUSTOMER]
    }
  },
  {
    path: "/my-tickets",
    name: "my-tickets",
    component: MyTicketsIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.CUSTOMER]
    }
  },
  {
    path: "/administrator/status",
    name: "status",
    component: StatusIndex,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/administrator/monitor",
    name: "monitor",
    component: MonitorForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/administrator/settings",
    name: "settings",
    component: SettingsForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/status/new",
    name: "status-new",
    component: StatusForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/status/edit/:id",
    name: "status-edit",
    component: StatusForm,
    props: true,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/report-tickets",
    name: "report-tickets",
    component: ReportTickets,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/delay-new",
    name: "delay-new",
    component: DelayForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/calculate-release-new",
    name: "calculate-release-new",
    component: CalculateReleaseForm,
    meta: {
      rolUserAllowed: [CONFIG.ROL.ADMIN, CONFIG.ROL.EMPLOYER]
    }
  },
  {
    path: "/trucker-to-tickets",
    name: "trucker-to-tickets",
    component: TruckerToTickets,
    meta: {
      rolUserAllowed: [CONFIG.ROL.TRUCKER, CONFIG.ROL.CUSTOMER]
    }
  }
];

const router = new Router({
  routes
});

router.beforeEach((to, from, next) => {
  if (!to.name) {
    next({ path: "/page404" });
  }

  if (to.name === "page404" || to.name === "pageNotAuthorized") {
    next();
  }

  if (_.size(to.meta)) {
    const routeAllowed =
      to.meta.rolUserAllowed &&
      to.meta.rolUserAllowed.indexOf(UserModel.data.role_id) !== -1;

    if (routeAllowed) {
      next();
    } else {
      next({ path: "/pageNotAuthorized", replace: false });
    }
  } else {
    next();
  }
});

export default router;
