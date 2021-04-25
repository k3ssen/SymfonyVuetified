import Vue from 'vue';

// Unfortunately, the VuetifyLoaderPlugin doesn't work with twig-vue-components (because these aren't preprocessed)
// So instead, here we import all VuetifyComponents. You may want to only import the Components you actually need.
import * as VuetifyComponents from 'vuetify/lib/components';
for (const i in VuetifyComponents) {
    Vue.component(i, VuetifyComponents[i]);
}

// Note that the components below aren't imported using "import * as components from '.components'"
// This is because importing the components like below will help IDE to provide autocompletion.

// FormTypes
Vue.component('SvCheckbox', () => import('./components/Form/SvCheckbox'));
Vue.component('SvCheckboxGroupType', () => import('./components/Form/SvCheckboxGroupType'));
Vue.component('SvChoice', () => import('./components/Form/SvChoice'));
Vue.component('SvCollection', () => import('./components/Form/SvCollection'));
Vue.component('SvDate', () => import('./components/Form/SvDate'));
Vue.component('SvForm', () => import('./components/Form/SvForm'));
Vue.component('SvFormWidget', () => import('./components/Form/SvFormWidget'));
Vue.component('SvHidden', () => import('./components/Form/SvHidden'));
Vue.component('SvPassword', () => import('./components/Form/SvPassword'));
Vue.component('SvRadio', () => import('./components/Form/SvRadio'));
Vue.component('SvRadioGroup', () => import('./components/Form/SvRadioGroup'));
Vue.component('SvRange', () => import('./components/Form/SvRange'));
Vue.component('SvSwitch', () => import('./components/Form/SvSwitch'));
Vue.component('SvTextarea', () => import('./components/Form/SvTextarea'));
Vue.component('SvText', () => import('./components/Form/SvText'));
Vue.component('SvFile', () => import('./components/Form/SvFile'));

// Other components
Vue.component('SvApp', () => import('./components/SvApp'));
Vue.component('SvFetch', () => import('./components/SvFetch'));
Vue.component('SvMenuItem', () => import('./components/SvMenuItem'));
