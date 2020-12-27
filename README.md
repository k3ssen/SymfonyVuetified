# Symfony Vuetified

A base project for creating a [Symfony](https://symfony.com/) application that uses
[Vuetify](https://vuetifyjs.com/en/) as frontend, while making it easy to pass serverside data.

This project is based on the [symfony/website-skeleton](https://github.com/symfony/website-skeleton)
with Symfony 5.x and the addition of `"symfony/webpack-encore-bundle": "*"`.

## Getting started

Assuming you run a server with php7.2.5+ (or 8.0+), mysql, composer, yarn (or npm) and required modules:

1. Checkout project
2. run`./init-project.sh`  
   You'll see Errors about missing packages when the script is trying to run Webpack.
   The script will try to install missing packages automatically.
   If this isn't working, then check step 12 in the manual steps.

Check the manual steps below if you're running into trouble.

### Manual steps (only needed if automatic setup failed)

3. run `composer install --ignore-platform-reqs`  
   --ignore-platform-reqs is needed for php8.0 at the moment of writing.
7. Add the following line at the top of your webpack.config.js file:
```js
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
```
And enable vue with jsx by adding the following in webpack.config.js:
 ```js
    //...
    .enableVueLoader(() => {}, {
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
 ```
(source: https://symfony.com/doc/current/frontend/encore/vuejs.html#jsx-support)

While not required for this setup, you may want to uncomment enableSassLoader and enableTypeScriptLoader.

8. Add the following line to your `assets/app.js` file:
```js
import './app-vue';
```
9. run `yarn add vuetify` (or use `npm install vuetify -P` instead)
10. run `yarn add sass sass-loader deepmerge vuetify-loader -D`
    (or use `npm install sass sass-loader deepmerge vuetify-loader -D` instead)
11. run `yarn install` (or use `npm install`)
12. Run `yarn dev` (or use `npm run dev`)  
    Add the additional packages that you'll see in the Error(s).
    Repeat this step until no further packages are required.

# Concept/usage

The aim is to make it easy to use Twig and Vue without resorting to API's or `data-` attributes in html.

## global vue object

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

> **Note:** Both Vue and Twig use `{{` and `}}` delimiters by default, so for Vue `@{` and `}` are used instead.
> You can specify different delimiters if you want, but avoid using `${` 
> like [Symfony's example](https://symfony.com/doc/5.2/frontend/encore/vuejs.html#using-vue-inside-twig-templates):
> if you pass twig content as template to vue you'll need to render the content between ticks (\`).
> When using ticks `${` will be parsed as javascript variable, causing unwanted behaviour.

## Passing data (`vue_data`)

When passing data,  you’ll often need to do things like this:

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

In addition to adding data to the vue-instance, data can also be added to the vue $store observable, making
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

Symfony's `FormView` can't be directly used in Vuejs,
so a Twig extension is created to enable serializing the FormView into json.
This can be passed to the `FormType` vue-component where it will render the form,
similar to twig's `{{ form(form) }}`.

Example:
```vue
{% block body %}
    {{ vue_data('form', form) }}
    <form-type :form="form"></form-type>
{% endblock %}
```

To take full control of your form-rendering you can also render parts individually:

```vue
{% block body %}
    {{ vue_data('form', form) }}
    <v-row>
        <v-col>
            <form-type :form="form.children.name"></form-type>
        </v-col>
        <v-col>
            <form-type :form="form.children.email"></form-type>
        </v-col>
    </v-row>
    <form-type :form="form"></form-type> <!-- renders remaining form-fields -->
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

> **Note:** The form components are just an example implementation to help get started.
> They haven't been perfectly refined for all kinds of different forms, so you may need to do
> some tweaking to make it suitable for your own project.
