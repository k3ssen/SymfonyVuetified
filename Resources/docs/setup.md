# SymfonyVuetifiedBundle

## Automatic setup

If you're in a new Symfony project you can run the following command
to have some modifications done automatically for you:
```
php bin/console symfony-vuetified:setup
```

## Manual setup

If you're in an existing Symfony project or running into trouble with the
automatic setup, then you need to make some file-modifications manually.

You'll need to modify `package.json`, `webpack.config.json`, `assets/app.js`, `templates/base.html.twig` and
add a `tsconfig.json` file to the root of your project.

**package.json**  

In the `package.json` file a dependency to this bundle should have been added automatically.
If it wasn't, make sure to add it yourself:

```
   "@k3ssen/symfony-vuetified": "file:vendor/k3ssen/symfony-vuetified/Resources/assets",
```

**webpack.config.js**  
Enable Typescript, Sass, Vuem Vuetify and rename `app.js` to `app.ts`.
```
const VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');
// [...]

Encore.
    // [...]
    .addEntry('app', './assets/app.ts')
    // [...]

    .enableSassLoader()
    .enableTypeScriptLoader()
    .enableVueLoader(() => {}, {
        useJsx: true
    })
    .addPlugin(new VuetifyLoaderPlugin())
    .configureLoaderRule('typescript', rule => {
        delete rule.exclude;
    })

// [...]
```

**tsconfig.json**  
To add the `tsconfig.json` file to the root of your project, you can copy it from this bundle:  
`cp vendor/k3ssen/symfony-vuetified/Resources/assets/tsconfig.json ./tsconfig.json`


**assets/app.js**  to **assets/app.ts**  
Rename `app.js` to `app.ts` if you haven't done so already and make the necessary modifications to support typescript.

Add `import 'k3ssen/symfony-vuetified';` to your `assets/app.js` file.

Alternatively you can use `import vueObject from './vue-object-init';` and use that vueObject in your own Vue instance
creation.

> though using app.ts instead of app.js is not a strict requirement, you'll want to use ts-files to prevent
[nasty 'cannot find module' errors](https://stackoverflow.com/questions/67925815/cannot-find-module-in-node-modules-folder-but-file-exist)


**templates/base.html.twig**  
If you have a new symfony project, you could replace the content of `base.html.twig` with the following:
```twig
{% extends '@SymfonyVuetified/base.html.twig' %}
```
Alternatively, you can use `{% include '@SymfonyVuetified/layout/_vue_script.html.twig' %}`
and add a `<div id="sv-app">` wrapper yourself.
Have a look at the 
[Resources/views/base.html.twig](../views/base.html.twig)
file of this bundle how this is done.


## Install the assets

Once setup is done you can run `yarn install` and `yarn dev`.

1. Start by running `yarn install`.
2. Make sure to add the peer-dependencies as well. Preferably you should check the output in your console, though
   the following should probably suffice:
    ```
    yarn --dev add vue@^2.5 vue-class-component@^7.2 vue-property-decorator@^9.1 vue-template-compiler@^2.6.10 vuetify@^2.4 vuetify-loader@^1.7
    ```
3. Run `yarn dev`  
   When you see an Error for missing required bundles, install that bundle and run `yarn dev` again.
   Keep repeating this process (about 4 or 5 times) until DONE.