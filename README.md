# SymfonyVuetifiedBundle

Integrate Vuetify into your Symfony application, making it easy to pass serverside data to Vue through Twig.

# Getting started

Assuming you have a Symfony 4.4 or 5+ application installed on a server with php7.1.3+ (or 8.0+), mysql, composer, yarn (or npm)
and required modules:

1. Run `composer require k3ssen/symfony-vuetified`  
   You may need to add `"minimum-stability": "dev", "prefer-stable": true` to your composer.json
2. Run `php bin/console symfony-vuetified:setup` if you just created a new symfony project.
   Otherwise, see 'configure files' below.
3. Run `yarn install & yarn dev`  
   When you see an Error for missing required bundles, install that bundle and run `yarn dev` again.
   Keep repeating this process (about 4 or 5 times) until DONE.

> Note: as of writing, Vuetify has sass-code that results in lots of deprecation warnings since sass version 1.33.  
> Run **yarn add sass@~1.32.0** to get rid of these deprecation warnings.

### Configure files
You'll need to modify `package.json`, `webpack.config.json`, `assets/app.js`, `templates/base.html.twig` and 
add a `tsconfig.json` file to the root of your project.

**package.json**  
Add this bundle to your dependencies in package.json:
```
   "@k3ssen/symfony-vuetified": "file:vendor/k3ssen/symfony-vuetified/Resources/assets",
```
(It might've been added automatically already. If it wasn't, make sure to add it yourself)

**webpack.config.js**  
Enable Typescript, Sass, Vue and Vuetify:
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
        This text is only shown if both objects have a value.
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
        This text is only shown if both objects have a value.
    </div>
{% endblock %}
```

## Global vue components
Custom and vendor Vue-components aren't global by default, so they can't be used in Twig.

The `globalComponents.js` file makes the components of Vuetify and this bundle globally available.
This way you can use these components almost wherever you want, including Twig.

The downside is that this'll increase the size of your javascript resources. 
If you want don't want to make all these components global, you can coose to use the 
`vue-object.init.ts` file instead of using `import '@k3ssen/symfony-vuetified'`.
Inside your `app.js`, it would look something like below:

```js
//... other stuff in your app.js file 

import Vue from 'vue';

import vueObject from '@k3ssen/symfony-vuetified/vue-object-init';

if (document.getElementById('sv-app') && typeof window.vue === 'object') {
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

Using form-functions in Twig or form_themes to create a Vuetify-form is difficult, especially when dealing with
edge-cases. This bundle introduces the `FormVue` class that processes Symfony's `FormView` to enable json_encoding.
This json is passed to the `sv_form` vue component to have the entire form rendered in Vue.

Example Usage:
```vue
{% block body %}
    {{ vue_data('form', form) }} {# json_encoding of the form is handled by the vueDataStorage #}
    <sv-form :form="form"></sv-form>
{% endblock %}
```

You can take full control and render parts individually. It takes some getting used to
it, because obviously Vue works differently from Twig's form-method, but it is quite powerful. 

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
<sv-form :form="form" v-slot="{ name, text }">
    <sv-text :form="name"></sv-text>
    <sv-textarea :form="text"></sv-textarea>
</sv-form>
```
Here the sv-text and sv-texteara components are used no matter what type that is provided by
the serverside form definition.

This last option can be useful when you want to use slots that are defined by Vuetify, since the
form-widget cannot cascade all slots (this would conflict with the default-slot).

## Custom form-type-components

To create a custom form-type, you should have a look at the `Resources/assets/components/Form` directory in this bundle.
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
