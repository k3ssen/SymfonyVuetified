# SymfonyVueBundle

A small SymfonyBundle that makes it easy to make serverside-data available to Vue.

Rather than using cumbersome `data-*` attributes or creating an API-endpoint for just about anything,
this bundle lets you pass data to vue by simply calling `{{ vue_data('someVariable', someObjectOrValue) }}`.

## Setup - quickstart

### Requirements

This bundle requires php 7.4 or higher. It has been developed with Symfony 6, but it should also work with Symfony 4 and 5.

### Getting started

Install the bundle:
`composer require k3ssen/symfony-vue-bridge`

You'll probably want to use setup VueJs with Symfony Encore 
(see [Setup with Encore](#setup-with-encore) for details below), but for quickly trying out this bundle
you can use the include-script below in your Twig code.
```
{% include '@SymfonyVue/_vue3_script.html.twig' %}
```

This will activate vue on the element with `id="app"`, so you'll need an element that has this id set on
it.


### Example
If you use 
[Symfony's MakerBundle](https://symfony.com/bundles/SymfonyMakerBundle/current/index.html) 
to run `php bin/console maker:controller Dashboard` you should have an 
`template/dashboard/index.html.twig` file that looks something like below:
```
{% extends 'base.html.twig' %}

{% block title %}Hello DashboardController!{% endblock %}

{% block body %}
    [...]
{% endblock %}
```

You can replace the body with the following:
```
{% block body %}
    <div id="app">
        {{ vue_data('count', 1) }}
        <button @click="count++" v-text="'Counter: ' + count"></button>
    </div>
    {% include '@SymfonyVue/_vue3_script.html.twig' %}
{% endblock %}
```

If you head to *yourlocaldoman/dashboard* you should see a button that increments the counter once
you click on it.


# Setup with Encore
You can find elaborate information on Symfony's guides for installing
[Encore](https://symfony.com/doc/current/frontend/encore/installation.html)
and [Enabling Vue](https://symfony.com/doc/current/frontend/encore/vuejs.html).

Basically, you'll need to do the following steps:

### 1. Install encore
 `composer require symfony/webpack-encore-bundle`


### 2. Enable Vue.js
Enable Vue in `webpack.config.js`:
```js
    // ...
    Encore
        // ...
        .enableVueLoader(() => {}, {
            runtimeCompilerBuild: true,
            useJsx: true
        })
    // ...
```

> **Tips:**
> * You'll probably want to also uncomment the `// enableSassLoader` in webpack.config.js to use scss
>   (which can be used in vue-components as well).
> * If you're a fan of Typescript, you should also uncomment `//.enableTypeScriptLoader()`
>    * Make sure to rename `assets/app.js` to `assets/app.ts` to prevent some
>      [nasty exceptions during yarn watch](https://stackoverflow.com/questions/67925815/cannot-find-module-in-node-modules-folder-but-file-exist)
> * If you have no plans of using Stimulus, you may want to remove (or comment out) `enableStimulusBridge`
 > and remove `assets/controllers`, `assets/controllers.json` and `assets/bootstrap.js`.

### 3. Install assets
Run `yarn install` followed by  `yarn dev`. 
You may see errors that you need to install some additional packages. Simply follow these instructions and re-run `yarn dev` until done.

### 4. Twig vue-javascript setup
The serverside data must be passed to vue, for which you can use the global `window` object.
For example, you can add the following code in your `base.html.twig`:
```
<div id="app">
    {% block body %}{% endblock %}
</div>
<script>
    window.vueData = {{ get_vue_data() | raw }};
    window.vueStoreData = {{ get_vue_store() | raw }};
</script>
```

The following things are relevant:
* the content you want to use Vue in is wrapped inside an element with the "app" id.
* `window.vueData` and `window.vueStoreData` must be created AFTER your content.
 (using `vue_add()` after this code won't have any effect).
* `window.vueData` and `window.vueStoreData` must be created BEFORE the app.js is loaded
  Encore uses `defer` on the script-tag by default, in which case this should work correctly.

### 5. Create Vue instance
Finally, you'll want to create a vue-instance that uses this data.
Open your `assets/app.js` to add some code (see below).


**Vue2**  
```
import Vue from 'vue';
// Make sure there is a vue-object-variable set on the global window-object.
window.vue = window.vue ?? {};
// Make sure this vue-object has a data property.
const vueObjectData = window.vue.data ?? {};
// Merge the vueObjectData (defined in Twig) with the data property that already exists on the vue-object.
window.vue.data = () => Object.assign(
    window.vueData,
    typeof vueObjectData === 'function' ? vueObjectData(): vueObjectData
);
const vueApp = createApp(window.vue);
// Create a reactive global $store variable which can be used in all components.
Vue.prototype.$store = Vue.observable(window.vueStoreData ?? {});
// Mount the app on the provided 'el'. If none provided, default to '#app'
window.vue.el = window.vue.el ?? '#app';
// Instantiate Vue.
new Vue(window.vue);
```

**Vue3**
```
import { createApp, reactive } from 'vue';
// Make sure there is a vue-object-variable set on the global window-object.
window.vue = window.vue ?? {};
// Make sure this vue-object has a data property.
const vueObjectData = window.vue.data ?? {};
// Merge the vueObjectData (defined in Twig) with the data property that already exists on the vue-object.
window.vue.data = () => Object.assign(
    window.vueData,
    typeof vueObjectData === 'function' ? vueObjectData(): vueObjectData
);
// Create the vue app
const vueApp = createApp(window.vue);
// Create a reactive global $store variable which can be used in all components.
vueApp.config.globalProperties.$store = reactive(window.vueStoreData ?? {});
// Mount the app on the provided 'el'. If none provided, default to '#app'
vueApp.mount(window.vue.el ?? '#app');
```

**Typescript**  
If you're using Typescript, you should edit `app.ts` instead. 
You can use basically the same code, but you'll need to make some small changes:
For example, `window.vue` probably won't be allowed, but you could use `window['vue']` instead.

## Usage

There are two key-points to this bundle and while they are not hard to implement, they can save a lot of time
compared to alternatives.

### Using a global Vue-object

Though complex Vue-logic should be written in Vue-components, there are times when you want to do
simple Vue-stuff inside Twig without any hassle.

By using a global object, this becomes fairly easy:
```
{% extends 'base.html.twig' %}

{% block body %}
    <div id="app">
        <h1>Seconds passed: ${ seconds }</h1>
        <p v-if="seconds > 5">
            More than 5 seconds have passed.
        </p>
    </div>
    <script>
        vue = {
            delimiters: ['${', '}'],
            data: () => ({
                seconds: 0,
            }),
            mounted() {
                setInterval(() => this.seconds++, 1000);
            },
        }
    </script>
{% endblock %}
```

Here you have the power of Vue and Twig at the same time.


### Passing server-side data to Vue

When you want to pass server-side data like an entity to Vue, you'd need something like this:
```
<script>
    vue = {
        data: () => ({
            someObject: {{ someObject | json_encode | raw }},
        })
    }
</script>
```

This works fine, but this has a bit too much boilerplate for simple scenario's where you only need to make data available.

To make this simple and more concise, this bundle adds these Twig functions:

* `vue_add('someObject', someObject)` - in practise has the same effect as the code above.
* `vue_store('someObject', someObject)` - to create a global `$store.someObject` variable than can be accessed in all components.
* `vue('someObject', someObject)` - does the same as vue_add, but it returns the key, so you can use this directly as a property.

There's also a `vue` filter that works like the vue-function, but it allows you to omit the key-name for objects:
the key name will be the same as the object variable name.
For example: `someObject|vue`


# Extending this bundle - custom logic

This bundle is more a guide to 'how to combine Vue and Twig' than lots of code. 
You *could* override the `VueDataStorage` and/or `VueExtension` services,
but it would probably the easiest to simply copy the files to your own project and have no
dependency on this bundle.
