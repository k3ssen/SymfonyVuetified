# Symfony Vuetified

A base project for creating a [Symfony](https://symfony.com/) application that uses
[Vuetify](https://vuetifyjs.com/en/) as frontend, while making it easy to pass serverside data.

This project is based on the [symfony/website-skeleton](https://github.com/symfony/website-skeleton)
with Symfony 5.x and the addition of `"symfony/webpack-encore-bundle": "*"`.

## Getting started

Assuming you run a server with php7.2.5+ (or 8.0+), mysql, composer, yarn (or npm) and required modules:

1. Checkout project
2. Run `composer install`
3. Run`./init-project.sh`    
   You'll see Errors about missing packages when the script is trying to run Webpack.
   The script will try to install missing packages automatically.
   If this isn't working, then check the last manual step.

Follow the manual steps below if you're running into trouble.

### Manual steps (only needed if automatic setup failed)

3. Make changes in `webpack.config.js` file to enable Typescript, Sass, Vue and Vuetify:
```js
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
const WebTypesPlugin = require('./assets/plugins/WebTypesPlugin');

// [...]

Encore.
    
    // [...]

    .enableSassLoader()
    .enableTypeScriptLoader()
    .enableVueLoader(() => {}, {
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
    .addPlugin(new WebTypesPlugin())

    // [...]
```
(more info: https://symfony.com/doc/current/frontend/encore/vuejs.html#jsx-support)

4. Add the following line to your `assets/app.js` file:
```js
import './main';
```
5. Run `yarn add vuetify`
6. Run `yarn add sass deepmerge vuetify-loader vue-property-decorator vue-class-component -D`
7. Run `yarn install`
8. Add `"web-types": "web-types.json"` to the root-object in `packages.json`  
    This will help PhpStorms (and possible other IDE's) recognize global vue objects. The `web-types.json` is a file
    containing info about the vue-components that will be generated by the `WebTypesPlugin` once you run webpack.
9. Run `yarn dev`
    Add the additional packages that you'll see in the Error(s).
    Repeat this step until no further packages are required.

# Concept/usage

This base projects aims to make it easy to use Twig and Vue without resorting to API's or `data-` attributes in HTML.

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
> When you use `${something}` this is parsed as javascript variable when used inside ticks ( \` ), which is needed sometimes.

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
Vue-components aren't global by default, so they can't be used in Twig.
By using the `.global.vue` extension instead of just `.vue` the
component will be made global, allowing you to use it inside Twig.

> Autocompletion in PhpStorm doesn't seem to work (yet?) for global components.
> This project lets webpack create a `web-types.json` to define references for the global components.
> It won't give autocompletion for props/slots/events, but at least you'll have autocompletion
> for the component name and reference to the file.

## Using Fetch

Because dynamic vue components can be rendered at runtime, the same principles can be used with `fetch` and load the
response in a component.

This project includes a FetchComponent that makes it really easy:
```vue
<fetch-component url="/url-to-controller-action"></fetch-component>
```

The `base.html.twig` file in this project checks if a fetch was used to choose the suitable file to extend:
if you're using fetch, only a template and the script will be loaded. Otherwise the entire page is loaded.

## Symfony's FormView as Vue component

Messing around in Twig to get the desired vuetify-forms is a pain.
It becomes especially troublesome when dealing with sub-forms and combo-boxes.

Instead of using Twig's `{{ form(form) }}`, this project creates a `VueForm` based on
Symfony's `FormView` to have it passed to vue. The rendering of the form can now be done
entirely in vue.

Example:
```vue
{% block body %}
    {{ vue_data('form', form) }}
    <vue-form :form="form"></vue-form>
{% endblock %}
```

To take full control you can also render parts individually:

```vue
{% block body %}
    {{ vue_data('form', form) }}
    <vue-form :form="form">
        <v-row>
            <v-col>
                <form-widget :form="form.children.name"></form-widget>
            </v-col>
            <v-col>
                <form-widget :form="form.children.email"></form-widget>
            </v-col>
        </v-row>
    </vue-form>
{% endblock %}
```

### Custom form-type-components

The `block_prefixes` are used to determine what component should be used for each individual field.
By setting a `block_prefix` in your FormType, you can specify a different component that you want to use for your
field. So within your form type class, you can use something like this:
```php
public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder->add('email', null, [
        'label' => 'Email',
        'block_prefix' => 'EmailType',
    ]);
}
```

After this you just need to make sure a component with the name `EmailType` has been defined where you at least
have a form property defined. To make it easier, just use the FormTypeMixin:
```vue
<template>
    <div>
        <label :for="form.vars.full_name">{{ form.vars.label }}</label>
        <input type="email" v-model="form.vars.data" v-bind="attributes" />
    </div>
</template>

<script>
    import {formTypeMixin} from "./FormTypeMixin";
    export default {
        mixins: [formTypeMixin],
    };
</script>
```

Have a look at the components in /assets/components/Form and you'll notice that most of these
aren't more complex than the example above.
