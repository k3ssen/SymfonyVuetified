### SymfonyVuetified

Example Symfony 5 project where Vuetified is used as frontend and twig can be used for
serverside rendering where its rendered content can be easiliy passed down to Vue.

### Getting started

1) Checkout project
2) Create a `.env.local` file to define `DATABASE_URL` for your environment.
3) run `yarn setup-all`  
(checkout packages.json for details and more shortcut commands)


* Development: use `yarn watch` to watch javascript/css files.


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

Symfony's FormView can't be directly used in Vuejs, so a
`JsonForm` php-class is created to enable serializing the FormView into json.
This can be passed to the `AppForm` vue-component where it will render
the form, somewhat like twig's `{{ form(form) }}` would do.

Example:
```
{% extends 'base.vue.twig' %}

{% block template %}
    <AppForm :form="form" />
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
```
    <AppForm :form="form.children.name" />
    <AppForm :form="form.children.email" />
    <AppForm :form="form" /> <!-- renders remaining form-fields -->
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
    <AppForm :form="$store.form" />
{% endblock %}
```


### Caveats
* Javascript in twig files is not compiled by webpack, so be extra aware of browser-compatibility.
Try to keep javascript complexity to a minimum by using actual Vue components wherever you can.
* No 'a-la-carte' for components in Twig. 
Vuetify-loader has a great a-la-carte system that'll automatically import vuetify components that are used in
your own components. Unfortunately this also requires webpack, so it won't work for components that are used
in twig files. You have to make sure you'll (globally) import any required component.
* Unlike vue components in `.vue` files, dynamic components that are created using twig-rendered data cannot
have scoped styles.
* When using vue and twig mixed together your IDE will probably be less capable of dealing with its content.
  TIP: use `.vue.twig` instead of `.html.twig` extensions to improve IDE support.