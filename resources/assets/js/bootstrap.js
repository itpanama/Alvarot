import Vue from "vue";
import ElementUI from "element-ui";
import locale from "element-ui/lib/locale/lang/en";
import VueResource from "vue-resource";
import VueCookie from 'vue-cookie';
import $ from 'jquery';
import _ from 'lodash';
import TreeView from "vue-json-tree-view"

Vue.use(TreeView);
Vue.use(VueResource);
Vue.use(ElementUI, { locale });
Vue.use(VueCookie);

Vue.filter("formatFileSize", bytes => {
  let val = bytes / 1024,
    suffix = "";

  if (val < 1000) {
    suffix = "KB";
  } else {
    val = val / 1024;
    if (val < 1000) {
      suffix = "MB";
    } else {
      val = val / 1024;
      if (val < 1000) {
        suffix = "GB";
      } else {
        val = val / 1024;
        suffix = "TB";
      }
    }
  }
  return Math.round(val) + suffix;
});

const token = document
  .querySelector("[name=csrf-token]")
  .getAttribute("content");

Vue.http.headers.common["X-CSRF-TOKEN"] = token;
Vue.prototype.$token = token;
Vue.prototype.$baseUrl = document
  .querySelector("[name=base-url]")
  .getAttribute("content");

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.Popper = require('popper.js').default;
    window.$ = $;
    window._ = _;

    // require('bootstrap');
} catch (e) {}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     encrypted: true
// });

export default Vue;