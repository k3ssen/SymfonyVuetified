import Vue from 'vue';

// Unfortunately, the VuetifyLoaderPlugin doesn't work with twig-vue-components (because these aren't preprocessed)
// So instead, here we import all VuetifyComponents. You may want to only import the Components you actually need.
import * as VuetifyComponents from 'vuetify/lib/components';
for (const i in VuetifyComponents) {
    Vue.component(i, VuetifyComponents[i]);
}

// All components that end with '.global.vue' are made globally available.

const requireComponent = require.context('./components', true, /[A-Z]\w+\.(g|glob|global)\.(vue|js)$/);

requireComponent.keys().forEach(fileName => {
    const componentConfig = requireComponent(fileName)
    const componentName = fileName.split('/').pop().replace(/\.(g|glob|global)\.\w+$/, '');
    // Register component globally
    Vue.component(componentName,componentConfig.default || componentConfig);
});
