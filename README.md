## SymfonyVuetified

Example Symfony 5 project where Vuetified is used as frontend and twig can be used for
serverside rendering where its rendered content can be easiliy passed down to Vue


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
the template for such dynamic-vue-component. And by using a global javascript
variable you can define your complete vue-object inside twig, like the
following:

```
{% extends 'base.vue.twig' %}
{% block template %}
    <p>
        { seconds } seconds have past since this page was loaded.
    </p>
{% endblock %}
{% block vueJs %}
    var vue = {
        data: () => ({
            seconds: 0,
        }),
        mounted() {
            setInterval(() => { this.seconds++; }, 1000);
        }
    };
{% endblock %}
```


### Dynamic twig form using Symfony's FormView

Symfony's `FormView` can't be directly used in Vuejs, so a `JsonForm` class is created to enable serializing the FormView into json.
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
                form: {{ jsonForm|raw }}
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
        'block_prefix' => 'MyEmailFormType',
    ]);
}
```

After this you just need to make sure a component with the name `MyEmailFormType` has been defined where you at least
have a form property defined:
```vue
<template>
    <div>
        <label :for="form.vars.full_name">{{ form.vars.label }}</label>
        <input :name="form.vars.full_name" :id="form.vars.full_name" type="email" v-model="form.vars.data" />
    </div>
</template>

<script>
    module.exports = {
        name: 'MyEmailFormType',
        props: {
            form: { type: Object},
        },
    }
</script>
```

This is of course just a lousy example. For something like an email-field you're probably better of with the
v-text-field that is used by default.

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