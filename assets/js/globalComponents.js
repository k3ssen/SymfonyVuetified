import Vue from 'vue';

// Unfortunately, the VuetifyLoaderPlugin doesn't work with twig-vue-components (because these aren't preprocessed)
// So instead, here we import all VuetifyComponents. You may want to only import the Components you actually need.
import * as VuetifyComponents from 'vuetify/lib/components';
for (const i in VuetifyComponents) {
    Vue.component(i, VuetifyComponents[i]);
}

// // Import all custom components that you need globally available.
import App from './components/App';
import Login from './components/Login';
import MenuItem from './components/MenuItem';
import Datatable from './components/Datatable';
import FormType from './components/Form/FormType';
import CollectionType from './components/Form/CollectionType';
import CollectionTableType from './components/Form/CollectionTableType';
import DateType from './components/Form/DateType';

// Adding all components to the object below may be somewhat cumbersome (compared to using something like
// 'import * as globalComponents from './components'), but by using the object below, the components will be recognized
// by the IDE.
const globalComponents = {
    App,
    Login,
    MenuItem,
    Datatable,
    FormType,
    CollectionType,
    CollectionTableType,
    DateType,
};

for (const i in globalComponents) {
    Vue.component(i, globalComponents[i]);
}
