// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

import Vue from 'vue';
import Vuetify from 'vuetify/lib';

Vue.use(Vuetify);
const vuetify = new Vuetify();

require('./globalComponents');
// require('./globalStoreAndMixin');

if (typeof vueObservableData !== 'undefined') {
    Vue.prototype.$store = Vue.observable(vueObservableData);
}

new Vue({
    el: '#app',
    vuetify,
});
