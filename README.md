## SymfonyVuetified

A Symfony 5 project to demonstrate how twig and vue can be mixed:

* Load twig rendered content as Vue-component.
* Pass serverside data into a vue store.
* Render Symfony's FormView as Twig components.
* Load dynamic component over ajax.


### Getting started

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


### Twig-vue-components

By using dynamic Vue components, rendered twig-content can be used to provide
the template for such dynamic-vue-component. 

```
{% extends 'base.vue.twig' %}
{% block template %}
    <p>
        { seconds } seconds have past since this page was loaded.
    </p>
{% endblock %}
{% block vueJs %}
    window.vue = {
        data: () => ({
            seconds: 0,
        }),
        mounted() {
            setInterval(() => { this.seconds++; }, 1000);
        }
    };
{% endblock %}
```

> Note that `window.vue` is purposely used instead of `var vue`. While for this example both have the same effect, using
> the window object makes it easier to redefine its value at different stages.


### Dynamic twig form using Symfony's FormView

Symfony's `FormView` can't be directly used in Vuejs, so a `VueForm` class is created to enable serializing the FormView into json.
This can be passed to the `FormType` vue-component where it will render the form, a bit similar to twig's `{{ form(form) }}`.

Example:

In your controller action you can pass the form like below:

```php
return $this->render('admin/user/new.vue.twig', [
    'jsonForm' => JsonForm::create($form),
]);
```
Then in your twig file can look like below:
```twig
{% extends 'base.vue.twig' %}

{% block template %}
    <FormType :form="form" />
{% endblock %}

{% block vueJs %}
    <script>
        const vue = {
            data: () => ({
                form: {{ vueForm|raw }}
            })
        }
    </script>
{% endblock %}
```

You can still render parts of the form individually, like this:
```vue
<FormType :form="form.children.name" />
<FormType :form="form.children.email" />
<FormType :form="form" /> <!-- renders remaining form-fields -->
```

### VueStore

Using the VueDataStorage class you can build an array that will be passed
to the vue observable, making this available to all vue components.
By using TwigExtensions, you can easily add data. 

This way the previous example could also be achieved like this:

```
{% extends 'base.vue.twig' %}

{% block template %}
    {{ vue_store('form', jsonForm) }}
    <FormType :form="$store.form" />
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

Just have a look at the components in /assets/js/components/Form and you'll notice that most of these aren't more
complex than the example above.


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
  TIP: use `.vue.twig` instead of `.html.twig` extensions to improve IDE support.