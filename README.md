# SymfonyVuetifiedBundle

Integrate Vuetify into your Symfony application, making it easy to pass serverside data through
twig-file.

# Getting started

Assuming you have a Symfony 4.4 or 5+ application installed on a server with php7.1.3+ (or 8.0+), mysql, composer, yarn (or npm)
and required modules:

1. Run `composer require k3ssen/symfony-vuetified`
2. Run `php bin/console symfony-vuetified:setup` if you just created a new symfony project.
   Otherwise see 'configure files' below.
3. Run `yarn install & yarn dev`  
   When you see an Error for missing required bundles, install that bundle and run `yarn dev` again.
   Keep repeating this process (about 4 or 5 times) until DONE.

### Configure files
You'll need to modify `webpack.config.json`, `assets/app.js`, `templates/base.html.twig` and 
add a `tsconfig.json` file to the root of your project.

**webpack.config.js**  
enable Typescript, Sass, Vue and Vuetify:
```
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
// [...]

Encore.
    // [...]

    .enableSassLoader()
    .enableTypeScriptLoader()
    .enableVueLoader(() => {}, {
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
    .configureLoaderRule('typescript', rule => {
        delete rule.exclude;
    })

// [...]
```

**tsconfig.json**  
To add the `tsconfig.json` file to the root of your project, you can copy it from this bundle:  
`cp vendor/k3ssen/symfony-vuetified/Resources/assets/tsconfig.json ./tsconfig.json`  


**assets/app.js**  
Add `import 'k3ssen/symfony-vuetified';` to your `assets/app.js` file.

Alternatively you can use `import vueObject from './vue-object-init';` and use that vueObject in your own Vue instance
creation.

**templates/base.html.twig**  
If you have a new symfony project, you could replace the content of `base.html.twig` with the following:
```twig
{% extends '@SymfonyVuetified/base.html.twig' %}
```
Alternatively, you can use `{% include '@SymfonyVuetified/layout/_vue_script.html.twig' %}` 
and add a `<div id="sv-app">` wrapper yourself.
Have a look at the *Resources/views/base.html.twig* file of this bundle how this is done.

# Concept/usage

This bundle aims to make it easy to use Twig and Vue without resorting to API's or `data-` attributes in HTML.

## Global vue object

The basic concept is that you can use a global vue object.
This object will be used for creating the vue-instance.

```vue
{% extends 'base.html.twig' %}
{% block body %}
    <p>
        @{ seconds } seconds have passed.
    </p>
{% endblock %}
{% block script %}
    <script>
        vue = {
            data: () => ({
                seconds: 0,
            }),
            mounted() {
                setInterval(() => {
                    this.seconds++;
                }, 1000);
            },
        };
    </script>
{% endblock %}
```

> **Note:** Vue and Twig both use `{{` and `}}` delimiters by default, so here `@{` and `}` are used instead for Vue.
> You can specify different delimiters if you want, but avoid using `${` 
> like [Symfony's example](https://symfony.com/doc/5.2/frontend/encore/vuejs.html#using-vue-inside-twig-templates):
> When you use `${something}` this is parsed as javascript variable when used inside ticks ( \` ), which 
> can be really confusing.

## Passing data (`vue_data`)

When passing data, you’ll often need to do things like below:

```vue
{% block script %}
    <script>
        vue = {
            data: () => ({
                someObject: {{ someObject | json_encode | raw }},
                anotherObject: {{ anotherObject | json_encode | raw }},
            })
        }
    </script>
{% endblock %}
```

If you need to pass server data to vue, you can use `vue_data` instead:

```vue
{% block body %}
    {{ vue_data('someObject', someObject) }}
    {{ vue_data('anotherObject', anotherObject) }}
    <div v-if="someObject && anotherObject">
        This tekst is only shown if both objects have a value.
    </div>
{% endblock %}
```

Data added this way will be json encoded and merged with the global vue object.


## Global observable (`$store` and `vue_store`)

In addition to adding data to the vue-instance, data can be added to the vue $store observable, making
data available to all vue components.
```vue
{% block body %}
    {{ vue_store('someObject', someObject) }}
    {{ vue_store('anotherObject', anotherObject) }}
    <div v-if="$store.someObject && $store.anotherObject">
        This tekst is only shown if both objects have a value.
    </div>
{% endblock %}
```

## Global vue components
Custom and vendor Vue-components aren't global by default, so they can't be used in Twig.

The `globalComponents.js` asset makes the components of Vuetify and this bundle globally available.
This way you can use these components wherever you want, also in twig.

The downside is that this'll increase the size of your javascript resources. 
If you want don't want to make all these components global, you can coose to use the 
`vue-object.init.ts` file instead of using `import '@k3ssen/symfony-vuetified'`.
Inside your `app.js`, it would look something like below:

```js
//... other stuff in your app.js file 

import Vue from 'vue';

import vueObject from '@k3ssen/symfony-vuetified/vue-object-init';

if (document.getElementById('vs-app') && typeof window.vue === 'object') {
    new Vue(vueObject);
}
```
Now only components of the vue-core will work in twig, so you won't be able
to use components like `<sv-form>` of `<v-alert>` in your twig files. 
If you want to make specific components available to twig, `<sv-app>` for example, you could use something like this:
```
Vue.component('SvApp', () => import('@k3ssen/symfony-vuetified/components/SvApp'));
```


# Using Fetch

Because dynamic vue components can be rendered at runtime, the same principles can be used with `fetch` and load the
response in a component.

This project includes a FetchComponent that makes it really easy:
```vue
<sv-fetch url="/url-to-controller-action"></sv-fetch>
```

If you're using `{% extends '@SymfonyVuetified/base.html.twig' %}` in your `base.html.twig`
then the suitable file to extend will be used:
if you're using fetch, only a template and the script will be loaded. Otherwise, the entire page is loaded.

> **Note:** this component requires loading the fetched javascript. 
> Fetching a page that defines variables/constants that were defined already will result in javascript-errors.
> Therefore, this bundle uses `window` to put global objects (like the global `vue`) into.  
> The `sv-fetch` component specifically takes the global objects vue, vueData, vueStoreData into account by clearing these
> objects before fetching new content.

# Symfony form as Vue component

Using form-functions in Twig or form_themes to create a Vuetify-form is conceptually easy, but very hard when dealing with
edge-cases. This bundle uses a `FormVue` class that json_encodes Symfony's `FormView`.
This json is passed to the vue component, `vue_form`, to have the entire form render by using Vue.

Example Usage:
```vue
{% block body %}
    {{ vue_data('form', form) }} {# json_encoding of the form is handled by the vueDataStorage #}
    <sv-form :form="form"></sv-form>
{% endblock %}
```

Just like you could in Twig, you can take full control and render parts individually. It takes some getting used to
it, because obviously Vue works differently, but it is quite powerful. 

There are several ways you can use to render different parts of your form:

**1) Using form-widget and full object paths**

```vue
<sv-form :form="form">
    <sv-form-widget :form="form.children.name"></sv-form-widget>
    <sv-form-widget :form="form.children.email"></sv-form-widget>
</sv-form>
```
**2) Using the `children` parameter provided by the default slot:**
```vue
<sv-form :form="form" v-slot="{ children }">
    <sv-form-widget v-for="(child, key) of children" :key="key" :form="child"></sv-form-widget>
</sv-form>
```
(this approach can be mixed with the first approach)  

**3) Using the child form names of default slot:**
```vue
<sv-form :form="form" v-slot="{ name, email }">
    <sv-form-widget :form="name"></sv-form-widget>
    <sv-form-widget :form="email"></sv-form-widget>
</sv-form>
```
(this approach can be mixed with the previous approaches)  

**4) Using subform slots for the children:**
```vue
<sv-form :form="form">
    <template v-slot:subform_name="{ subform }">
        <sv-form-widget :form="subform"></sv-form-widget>
    </template>
    <!-- email is still rendered by the sv-form -->
</sv-form>
```
The `subform_` prefix is being used here to make sure slots won't overlap (e.g. when a field is called 'default').

Subform slots are defined within the default slot, so this approach cannot be combined with the previous ones.
Doing so would result in these subform slots being ignored by vue.

Using subform slots can be useful when you want to make changes for specific fields but don't need to change
the rendering (or order) of other fields.


**5) Using specific component types:**
```vue
<sv-form :form="form" v-slot="{ name, email }">
    <sv-text :form="name"></sv-text>
    <sv-textarea :form="email"></sv-textarea>
</sv-form>
```
Here the text-type and texteara-types components are used despite the 'type' that is provided by
the serverside form definition.

This last option can be useful when you want to use slots that are defined by Vuetify, since the
form-widget cannot cascade all slots (this would conflict with the default-slot).

## Custom form-type-components

To create a custom form-type, you should have a look at the `/assets/components/Form` directory.
Most of the logic you need is put in the `FormTypeMixin`, so you probably want to extend that to have some stuff taken
care of. 

For example, if you want to create a wysiwyg text-editor using quill, you could create a EditorType:
```vue
<template>
    <div class="text-editor-type">
        <!-- use a hidden field to use when submitting the form -->
        <input type="hidden" :name="form.vars.full_name" :value="form.vars.data" />
        <quill-editor
            ref="myTextEditor"
            v-model="form.vars.data"
        />
    </div>
</template>

<script lang="ts">
    import {Component, Mixins} from 'vue-property-decorator';
    import FormWidgetMixin from "./FormWidgetMixin.ts";
    import quillEditor from 'vue-quill-editor';
    
    @Component
    export default class EditorType extends Mixins(FormWidgetMixin) {
        created() {
            // do stuff like applying settings to the editor...
        }
    }
</script>
```

Now you could use this component directly by using something like `<editor-type :form="form"></editor-type>`.


The block_prefixes are used to determine what component should be used for each individual field.
By setting a `block_prefix` in your FormType, you can specify a different component that you want to use for your
field. So within your form type class, you can use something like this:
```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder->add('text', null, [
        'label' => 'Text',
        'block_prefix' => 'EditorType',
    ]);
}
```
