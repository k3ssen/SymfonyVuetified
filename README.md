# Symfony Vuetified

A [Symfony](https://symfony.com/) 5 demo-project that utilizes the
[SymfonyVuetified](https://github.com/k3ssen/SymfonyVuetified/tree/bundle)
bundle

### Getting started

Assuming you run a server with php7.2.5+ (or 8.0+), mysql, composer, yarn (or npm) and required modules:

1. `composer create-project k3ssen/symfony-vuetified:dev-demo`
2. `php bin/console symfony-vuetified:setup -n && ./init-project.sh`  
   If the init-project.sh isn't working, then you can do the following steps manually:
    1. `yarn install`
    2. `yarn dev`
    3. You'll see an error for missing packages: install these packages
       and keep repeating these last two steps until all required packages are installed.

### Contents

In this demo you'll find how you can utilize the `sv-form`, including different types,
and the `sv-fetch` components simply by providing information through the `vue_data` twig-extension.

An example is created by running `php bin/console make:entity Book` and `php bin/console make:crud Book`,
after which it only takes minor effort (about a minute) to get to this:

![Screenshot](https://github.com/k3ssen/SymfonyVuetified/blob/demo/screenshot.png)

A form like below can be rendered entirely using Vuetify and it only takes these lines in your twig-code:
```
{{ vue_data('form', form) }}
<sv-form :form="form" label-submit="{{ button_label|default('Save') }}"></sv-form>
```

![Form Screenshot](https://github.com/k3ssen/SymfonyVuetified/blob/demo/form-screenshot.png)

There's no weird server-side stuff you need to do to get to this result. After
running Symfony's make-command, only the meta-field was edited to define that a collection should be used:
```phpr
$builder
    ->add('title')
    ->add('publishedAt')
    ->add('numberOfPages')
    ->add('meta', CollectionType::class, [
        'entry_type' => BookMetaType::class,
        'entry_options' => [
            'label' => false,
        ],
    ])
    ->add('author')
;
```

And the buildForm method in `BookMetaType` only contains:
```
$builder
    ->add('name')
    ->add('value')
;
```

Simply put, in your serverside code you can build forms like you're used to.
However, certain attributes or options were created specifically for twig's form-rendering
may be ignored by the `sv-form` component, because they haven't been implemented.