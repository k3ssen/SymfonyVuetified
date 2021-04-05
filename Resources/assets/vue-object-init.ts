import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);
const vuetify = new Vuetify();

const windowGlobals = (window as any);

// Enable vueStoreData to be passed as global variable into the $store.
Vue.prototype.$store = Vue.observable(windowGlobals.vueStoreData || {});

// Make window object accessible to vue-components (e.g. usage within templates)
Vue.prototype.$window = window;

if (typeof windowGlobals.vue === 'object') {

    if (typeof windowGlobals.vueData !== 'undefined') {
        const vueObjectData = windowGlobals.vue.data ?? {};
        windowGlobals.vue.data = () => (Object.assign(
            windowGlobals.vueData,
            typeof vueObjectData === 'function' ? vueObjectData(): vueObjectData
        ));
    }
} else {
    windowGlobals.vue = {};
}

const vueObject = Object.assign({
    vuetify,
}, windowGlobals.vue ?? {});

export default vueObject;
