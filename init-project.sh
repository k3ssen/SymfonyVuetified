#!/bin/bash

for ARGUMENT in "$@"
do
    KEY=$(echo $ARGUMENT | cut -f1 -d=)
    VALUE=$(echo $ARGUMENT | cut -f2 -d=)

    case "$KEY" in
            --php)              php=${VALUE} ;;
            *)
    esac
done

echo "php version = ${php}"

command_exists () {
    type "$1" &> /dev/null ;
}

if command_exists ddev ; then
    ddev config --project-type=laravel --php-version="${php}" --docroot=public --create-docroot &&

  # create bash file to open database with heidisql
    cat > ./.ddev/commands/host/heidisql << ENDOFFILE
#!/bin/bash

## Description: Run Heidisql (in Windows) against current db
## Usage: heidisql
## Example: "ddev heidisql"

'/mnt/c/Program Files/HeidiSQL/heidisql.exe' --user=root --host="127.0.0.1" --port=${DDEV_HOST_DB_PORT} --password=root

ENDOFFILE

  # create bash file to add command 'ddev c' as shortcut for 'ddev ssh' -> 'php bin/console'
    cat > ./.ddev/commands/web/binconsole << ENDOFFILE
#!/bin/bash

## Description: Execute php bin/console in the web container
## Usage: c
## Example: "ddev c list"

php bin/console $@
ENDOFFILE

  # create bash file to add command 'ddev c' as shortcut for 'ddev ssh' -> 'yarn'
    cat > ./.ddev/commands/web/yarn << ENDOFFILE
#!/bin/bash

## Description: Execute yarn in the web container
## Usage: yarn
## Example: "yarn install"

yarn $@
ENDOFFILE

  # create .enc.local file'
    cat > ./.env.local << ENDOFFILE
DATABASE_URL=mysql://db:db@db:3306/db?serverVersion=5.7
ENDOFFILE

    ddev start
fi


if command_exists ddev ; then
    ddev composer install --ignore-platform-reqs
else
    composer install --ignore-platform-reqs
fi

echo "Updating assets/app.js";
echo >> ./assets/app.js  &&
echo "import './app-vue';" >> ./assets/app.js  &&

echo "Updating webpack.config.js for enableSassLoader, enableVueLoader, enableTypeScriptLoader";

# add VuetifyLoaderPlugin constant
line_old="webpack-encore');"
line_new="webpack-encore');\nconst VuetifyLoaderPlugin = require('vuetify-loader/lib/plugin');"
sed -i "s%$line_old%$line_new%g" ./webpack.config.js &&

# enable sassloader, vueLoader with jsx and add VuetifyLoaderPlugin as plugin
line_old='//.enableSassLoader()'
line_new='.enableSassLoader()\n    .enableVueLoader(() => {}, {\n        useJsx: true\n    })\n    .addPlugin(new VuetifyLoaderPlugin())'
sed -i "s%$line_old%$line_new%g" ./webpack.config.js &&

# enable typescript
line_old='//.enableTypeScriptLoader()'
line_new='.enableTypeScriptLoader()'
sed -i "s%$line_old%$line_new%g" ./webpack.config.js &&

# disable stimulus (we're using vue already)
line_old='.enableStimulusBridge()'
line_new='//.enableStimulusBridge()'
sed -i "s%$line_old%$line_new%g" ./webpack.config.js &&

if command_exists ddev ; then
      # it's risky to add vuetify without specifying any version, but otherwise the process can't continue.
    # check https://vuetifyjs.com/en/getting-started/installation/#webpack-install for requirements
    ddev yarn add vuetify &&
    ddev yarn add sass sass-loader deepmerge vuetify-loader -D &&
    ddev yarn install &&
    ddev yarn dev
else
    if command_exists yarn ; then
        yarn add vuetify &&
        yarn add sass sass-loader deepmerge vuetify-loader -D &&
        yarn install &&
        yarn dev
    else
        npm install vuetify -P &&
        npm install  sass sass-loader deepmerge vuetify-loader -D &&
        npm install &&
        npm run dev
    fi
fi