import Vue from 'vue';
import './globalComponents';

import vueObject from './vue-object-init';

if (document.getElementById('sv-app') && typeof window.vue === 'object') {
    new Vue(vueObject);
}
