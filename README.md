
## Getting started

1. clone project
2. cd into the project
3. run `ddev config --project-type=laravel --php-version="8.0" --docroot=public --create-docroot`
4. run `ddev start`
5. run `ddev composer install --ignore-platform-reqs`
6. enable vue with jsx by adding the following in webpack.config.js:  
 ```js
   // ...
   .enableVueLoader(() => {}, {
       useJsx: true
   })
 ```
(source: https://symfony.com/doc/current/frontend/encore/vuejs.html#jsx-support)

7. Add the following line to your `assets/app.js` file:
```js
import './app-vue';
```
9. run `ddev yarn install`  
   Add the additional packages that you'll see in the warnings.
   Repeat this progress until no further packages are required.
10. Run `ddev yarn watch`.

Run `ddev launch` to open the website.
