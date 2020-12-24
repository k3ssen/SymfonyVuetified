# Symfony Vuetified

A base project for creating a [Symfony](https://symfony.com/) application that uses
[Vuetify](https://vuetifyjs.com/en/) as frontend, while making it easy to pass data from Twig.

This project is based on the [symfony/website-skeleton](https://github.com/symfony/website-skeleton)
with Symfony 5.x and the addition of `"symfony/webpack-encore-bundle": "*"`.

## Getting started

### Requirements

The setup was created/tested with [DDEV-local](https://ddev.readthedocs.io/en/stable/).
If you're running your own development environment, then you at least need to have php,
composer and yarn (or npm) installed.

### Setup

1. Clone the project
2. `cd` into the project
3. run`./init-project.sh --php=7.4`   
   Use a different php version if you want (only 7.4 and 8.0 have been tested; at the very least you need php 7.2.5).

If you run into trouble, you could follow the manual setup below.
   
### Manual setup (only needed if step 3 failed)

3. run `composer install --ignore-platform-reqs`  
   --ignore-platform-reqs is need for php8.0 at the moment of writing.
   You should omit it for php7.4.
7. Add the following line at the top of your webpack.config.js file:
```js
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
```
And enable vue with jsx by adding the following in webpack.config.js:  
 ```
    //...
    .enableVueLoader(() => {}, {
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
 ```
(source: https://symfony.com/doc/current/frontend/encore/vuejs.html#jsx-support)

While not required for this setup, you may want to uncomment enableSassLoader and enableTypeScriptLoader.
You also might want make a comment of enableStimulusBridge to have that disabled.

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

The aim is to make it easy to use Twig and Vue without resorting to API's or
cumbersome 'data-' attributes in html.

## global vue object

The basic concept is that you can use a global vue object. This
object will be used for creating the vue-instance.

```twig
{% extends 'base.vue.twig' %}
{% block body %}
    <p>
        { seconds } seconds have passed.
    </p>
{% endblock %}
{% block script %}
    <script>
        vue = {
            data: () => ({
                seconds: 0,
            }),
            mounted() {
                setInterval(() => { this.seconds++; }, 1000);
            },
        };
    </script>
{% endblock %}
```

> Note that Vue and Twig both use {{ and }} delimiters by default, so for Vue a single { is being used here.
> (you can easily use other delimiters if you want)

## Passing data (`vue_data`) 

When passing data,  you’ll often need to do things like this:

```twig
{% block body %}
    <div v-if="someObject && anotherObject">
        This tekst is only shown if both objects have a value.
    </div>
{% endblock %}
{% block script %}
    <script>
        vue = {
            data: () => ({
                someObject: {{ someObject | json_encode | raw }},
                anotherObject: {{ anotherObject | json_encode | raw }},
            }),
        }
    </script>
{% endblock %}
```

If you just need to pass data to vue like this, you can use `vue_data` instead:

```twig
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
```twig
{% block body %}
    {{ vue_store('someObject', someObject) }}
    {{ vue_store('anotherObject', anotherObject) }}
    <div v-if="$store.someObject && $store.anotherObject">
        This tekst is only shown if both objects have a value.
    </div>
{% endblock %}
```


## Using Fetch

Because dynamic vue components can be rendered at runtime, the same principles can be used with `fetch` and load the response in a component.

This project includes a FetchComponent that makes it really easy:
```
<fetch-component url="/url-to-controller-action"></fetch-component>
```


## Symfony's FormView as Vue component

Symfony's `FormView` can't be directly used in Vuejs,
so a Twig extension is created to enable serializing the FormView into json.
This can be passed to the `FormType` vue-component where it will render the form,
similar to twig's `{{ form(form) }}`.

Example:
```twig
{% block body %}
    {{ vue_add('form', form) }}
    <form-type :form="form"></form-type>
{% endblock %}
```

To take full control of your form-rendering you can also render parts individually:

```twig
{% block body %}
    {{ vue_add('form', form) }}
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
```
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

Have a look at the components in /assets/components/Form and you'll notice that most of these aren't more
complex than the example above.
