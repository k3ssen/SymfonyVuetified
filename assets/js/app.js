import '../css/app.css'; // any CSS you import will output into a single app.css file
import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);
const vuetify = new Vuetify();

require('./globalComponents');

// Enable store to be passed as global variable into the $store.
if (typeof store !== 'undefined') {
    Vue.prototype.$store = Vue.observable(store);
}
// Make window object accessible to vue-components (e.g. usage within templates)
Vue.prototype.$window = window;

new Vue({
    el: '#app',
    vuetify,
});

// Make Vue globally available
window.Vue = Vue;