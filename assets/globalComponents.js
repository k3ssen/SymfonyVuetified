import Vue from 'vue';

// Unfortunately, the VuetifyLoaderPlugin doesn't work with twig-vue-components (because these aren't preprocessed)
// So instead, here we import all VuetifyComponents. You may want to only import the Components you actually need.
import * as VuetifyComponents from 'vuetify/lib/components';
for (const i in VuetifyComponents) {
    Vue.component(i, VuetifyComponents[i]);
}

// Note that the custom components below aren't imported using "import * as components from '.components'"
// This is because importing the components like below will help IDE to provide autocompletion.

// FormWidgets
Vue.component('CheckboxGroupType', () => import('./components/Form/CheckboxGroupType'));
Vue.component('CheckboxType', () => import('./components/Form/CheckboxType'));
Vue.component('ChoiceType', () => import('./components/Form/ChoiceType'));
Vue.component('CollectionType', () => import('./components/Form/CollectionType'));
Vue.component('FormWidget', () => import('./components/Form/FormWidget'));
Vue.component('DateType', () => import('./components/Form/DateType'));
Vue.component('HiddenType', () => import('./components/Form/HiddenType'));
Vue.component('PasswordType', () => import('./components/Form/PasswordType'));
Vue.component('RadioGroupType', () => import('./components/Form/RadioGroupType'));
Vue.component('RadioType', () => import('./components/Form/RadioType'));
Vue.component('SwitchType', () => import('./components/Form/SwitchType'));
Vue.component('TextareaType', () => import('./components/Form/TextareaType'));
Vue.component('TextType', () => import('./components/Form/TextType'));

// Other components
Vue.component('App', () => import('./components/App'));
Vue.component('FetchComponent', () => import('./components/FetchComponent'));
Vue.component('MenuItem', () => import('./components/MenuItem'));
