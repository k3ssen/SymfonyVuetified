# SymfonyVuetifiedBundle

Integrate Vuetify into your Symfony application, making it easy to pass serverside data to Vue through Twig.

# Getting started

Assuming you have a Symfony 4.4, 5+ or 6+ application installed on a server with composer, yarn (or npm)
and required modules:

1. Run `composer require k3ssen/symfony-vuetified`
2. Run `php bin/console symfony-vuetified:setup` if you just created a new symfony project.
   Otherwise, see the [manual setup](./Resources/docs/setup.md) documentation.
3. Install dependencies
   1. Start by running `yarn install`.
   2. Make sure to add the peer-dependencies as well. Preferably you should check the output in your console, though
   the following should probably suffice:  
       `
       yarn --dev add vue@^2.5 vue-class-component@^7.2 vue-property-decorator@^9.1 vue-template-compiler@^2.6.10 vuetify@^2.4 vuetify-loader@^1.7
       `
   3. Run `yarn dev`  
   When you see an Error for missing required bundles, install that bundle and run `yarn dev` again.
   Keep repeating this process (about 5 times) until DONE.


# Concept/usage

This bundle aims to quickly achieve the following:
* Communicate serverside data from Twig to Vue without resorting to API's or `data-` attributes in HTML.
* Build your vue-components using Vuetify, Typescript and the [vue-property-decorator](https://npm.io/package/vue-property-decorator).
* Render your Symfony form in Vuetify by using client-side form-rendering.
* Render dynamic vue components that are fetched and loaded via twig-files.

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

## Passing serverside data to vue: `vue_data`

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


## Global observable: `$store` and `vue_store`

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

The `globalComponents.ts` file makes the components of Vuetify and this bundle globally available.
This way you can use these components almost wherever you want, including Twig.

The downside is that this'll increase the size of your javascript resources. 
If you want don't want to make all these components global, you can choose to use the 
`vue-object.init.ts` file instead of using `import '@k3ssen/symfony-vuetified'`.
Inside your `app.ts`, it would look something like below:

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

# Fetch component

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


## Symfony form rendered in Vue

Using form-functions in Twig or form_themes to create a Vuetify-form is difficult, especially when dealing with
edge-cases. This bundle enables you to render Symfony's `FormView` clientside:

```twig|vue
{% block body %}
    <sv-form :form="{{ form | vue}}"></sv-form>
{% endblock %}
```

You can take full control and render parts individually. It takes some getting used to
it, because obviously Vue works differently from Twig's form-method, but it is quite powerful.

[Read the forms documentation](./Resources/docs/forms.md) for more information.