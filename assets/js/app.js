// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';
import Vue from 'vue';
import Vuetify from 'vuetify/lib';
import * as GlobalComponents from './globalComponents';

Vue.use(Vuetify);
const vuetify = new Vuetify();

// All components that are exported from GlobalComponents are added as global vue component.
for (const i in GlobalComponents) {
    Vue.component(i, GlobalComponents[i]);
}

// Enable vueObservableData to be passed as global variable into the $store.
if (typeof vueObservableData !== 'undefined') {
    Vue.prototype.$store = Vue.observable(vueObservableData);
}
// Make window object accessible to vue-components (e.g. usage withing templates)
Vue.prototype.$window = window;

new Vue({
    el: '#app',
    vuetify,
});

window.Vue = Vue;