// Unfortunately, the VuetifyLoaderPlugin doesn't work with twig-vue-components (because these aren't preprocessed)
// So instead, here we import all VuetifyComponents. You might want to only import the Components you actually need.
export * from 'vuetify/lib/components';

// Any component added to the export below will be added as globalComponent in app.js
export {default as App} from './components/App';
export {default as Login} from './components/Login';
export {default as MenuItem} from './components/MenuItem';
export {default as Datatable} from './components/Datatable';

// All Form components
export {default as CheckboxGroupType} from './components/Form/CheckboxGroupType';
export {default as CheckboxType} from './components/Form/CheckboxType';
export {default as ChoiceType} from './components/Form/ChoiceType';
export {default as CollectionType} from './components/Form/CollectionType';
export {default as DateType} from './components/Form/DateType';
export {default as FormType} from './components/Form/FormType';
export {default as HiddenType} from './components/Form/HiddenType';
export {default as PasswordType} from './components/Form/PasswordType';
export {default as RadioGroupType} from './components/Form/RadioGroupType';
export {default as RadioType} from './components/Form/RadioType';
export {default as TextareaType} from './components/Form/TextareaType';
export {default as TextType} from './components/Form/TextType';
export {default as SwitchType} from './components/Form/SwitchType';