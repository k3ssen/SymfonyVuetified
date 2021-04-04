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
