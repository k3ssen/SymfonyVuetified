import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);
const vuetify = new Vuetify();

import './globalComponents';

const global = (window as any);

// Enable store to be passed as global variable into the $store.
let store = {};
if (typeof global.vueStoreData !== 'undefined') {
    store = Vue.observable(global.vueStoreData);
}
Object.defineProperty(Vue.prototype, '$store', {value: store});


// Make window object accessible to vue-components (e.g. usage within templates)
Vue.prototype.$window = window;

if (typeof global.vue === 'object') {

    if (typeof global.vueData !== 'undefined') {
        const vueObjectData = global.vue.data ?? {};
        global.vue.data = () => (Object.assign(
            global.vueData,
            typeof vueObjectData === 'function' ? vueObjectData(): vueObjectData
        ));
    }

    new Vue(Object.assign({
        vuetify,
        store,
    }, global.vue));
}
