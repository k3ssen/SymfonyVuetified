## SymfonyVuetified

A Symfony 5 project to demonstrate how Twig and Vue can be mixed.


## Getting started

Assuming you run a server with php7.4+, mysql, composer, yarn and required modules:

1) Checkout project
2) Create a `.env.local` file to define `DATABASE_URL` for your environment.
3) run `yarn setup-all` or run the following commands step by step:
    1) `composer install`  
    2) `yarn install`  
    3) `yarn build` (for production) or `yarn dev` (for development)  
    4) `php bin/console doctrine:database:create`  
    5) `php bin/console doctrine:schema:create`  
    6) `php bin/console doctrine:fixtures:load -n`

For development you can use `yarn watch` to watch javascript/css files.


## Twig-vue-components


### Concept

Thanks to Twig's flexible way of dealing with templates it become quite easy to put 
a twig block inside a vue object:
```
{% block javascripts }%
    {% block script}{% endblock %}
    <script>
        window.vue = Object.assign({
            template: `<div>{{ block('body') }}</div>`,
            delimiters: ['{', '}']
        }, typeof window.vue !== 'undefined' ? window.vue : {})
    </script>
{% endblock %}
```

This will render the content of a body-block inside the template. It is wrapped inside
a div to ensure there's only one root-tag.
Because twig already uses `{{` we redefine the vue-delimiters to only use one `{`.

Furthermore we check if the object has already been defined, so we merge its contents.

Inside Vue, all you need to do is put the object in a dynamic component:

```vue
<component :is="$window.vue"></component>
```

> The `$window` isn't available by default, but it can be easily made accessible by using 
`Vue.prototype.$window = window;`.

### Usage

Assuming you've put the part where the body block is put in the vue object inside 
the 'base.vue.twig' file you can use it other files by simply extending it:

```
{% extends 'base.vue.twig' %}
{% block body %}
    <p>
        { seconds } seconds have past since this page was loaded.
    </p>
{% endblock %}
{% block script %}
    <script>
        window.vue = {
            data: () => ({
                seconds: 0,
            }),
            mounted() {
                setInterval(() => { this.seconds++; }, 1000);
            }
        };
    </script>
{% endblock %}
```


### Using Fetch

Because dynamic vue components can be rendered at runtime, the same principles can be used
with `fetch` and load the response in a component. 

This project includes a `FetchComponent` that makes it really easy:

```
<fetch-component :url="/url-to-controller-action"></fetch-component>
```

By utilizing both the flexibility of twig and vue this enables you to load pages
where the body is passed over the window.vue object, but it also enables you
to load only fetch the body for smaller requests.


### Caveats
* Javascript in twig files is not compiled by webpack, so be extra aware of browser-compatibility.
Try to keep javascript complexity to a minimum by using actual Vue components wherever you can.
* No 'a-la-carte' for components in Twig. 
Vuetify-loader has a great a-la-carte system that'll automatically import vuetify components that are used in
your own components. Unfortunately this also requires webpack, so it won't work for components that are used
in twig files. You have to make sure to import any required component. You can use the `/assets/js/globalComponents.js`
to make components globally available.
* Unlike components in `.vue` files, dynamic components that are created using twig-rendered data cannot
have scoped styles.
* When using vue and twig mixed together your IDE will probably be less capable of dealing with its content.
  TIP: using `.vue.twig` instead of `.html.twig` extensions can help improve IDE support.

## Symfony's FormView as Vue component

Symfony's `FormView` can't be directly used in Vuejs, so a Twig extension is created to enable serializing 
the FormView into json. 
This can be passed to the `FormType` vue-component where it will render the form, similar to twig's `{{ form(form) }}`.

Example:

```twig
{% extends 'base.vue.twig' %}

{% block body %}
    <FormType :form="form" />
{% endblock %}

{% block script %}
    <script>
        const vue = {
            data: () => ({
                form: {{ vue_form(form)|raw }}
            })
        }
    </script>
{% endblock %}
```

To take full control of your form-rendering you can also render parts individually:
```vue
<v-row>
    <v-col>
        <FormType :form="form.children.name" />
    </v-col>
    <v-col>
        <FormType :form="form.children.email" />
    </v-col>
</v-row>
<FormType :form="form" /> <!-- renders remaining form-fields -->
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

Just have a look at the components in /assets/js/components/Form and you'll notice that most of these aren't more
complex than the example above.


## VueStore

Using the VueDataStorage class you can build an array that will be passed
to the vue observable, making this available to all vue components.
The `vue_store` added by the twig extension lets you easily add data. 

Now rendering a form could also be achieved like this:

```
{% extends 'base.vue.twig' %}

{% block body %}
    {{ vue_store('form', jsonForm) }}
    <FormType :form="$store.form" />
{% endblock %}
```

The store provides a powerful way of communicating data: even components that are fetched later can alter data
in the store. This can provide nice possibility such as modifying your menu-items or breadcrumbs based on the page
you just fetched.
