import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);
const vuetify = new Vuetify();

require('./globalComponents');

// Enable store to be passed as global variable into the $store.
if (typeof vueStoreData !== 'undefined') {
    Vue.prototype.$store = Vue.observable(vueStoreData);
}
// Make window object accessible to vue-components (e.g. usage within templates)
Vue.prototype.$window = window;

if (typeof vue === 'object') {

    if (typeof vueData !== 'undefined') {
        const vueObjectData = vue.data ?? {};
        vue.data = () => (Object.assign(
            vueData,
            typeof vueObjectData === 'function' ? vueObjectData(): vueObjectData
        ));
    }

    new Vue(Object.assign({
        el: '#app',
        delimiters: ['@{', '}'],
        vuetify,
    }, vue));
}

// Make Vue globally available
window.Vue = Vue;
