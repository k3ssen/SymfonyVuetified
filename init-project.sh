#!/bin/bash

command_exists () {
    type "$1" &> /dev/null ;
}

composer install --ignore-platform-reqs

echo "Updating assets/app.js";
echo >> ./assets/app.js  &&
echo "import './main';" >> ./assets/app.js  &&

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
#line_old='.enableStimulusBridge()'
#line_new='//.enableStimulusBridge()'
#sed -i "s%$line_old%$line_new%g" ./webpack.config.js &&


# capture the output of a command so it can be retrieved with ret
cap () {
  tee /tmp/capture.out;
}

count=0;
runYarnDev() {
  ((count++)); # use count to prevent infinite loop in case something goes wrong
  if [[ $count < 8 ]]; then
    yarn dev | cap;
    string=$(cat /tmp/capture.out);
    if [[ "$string" == *"yarn add "* ]]; then
      yarnAddCommand=$(echo ${string} | sed 's/.*\(yarn add [a-zA-Z0-9.@^ -/]*\( \-\-dev\)*\).*/\1/');
      echo "Trying to automatically add package(s) by executing: ${yarnAddCommand}";
      eval ${yarnAddCommand};
      runYarnDev;
    fi
  fi
}

runNpmDev() {
  ((count++)); # use count to prevent infinite loop in case something goes wrong
  if [[ $count < 8 ]]; then
    npm run dev | cap;
    string=$(cat /tmp/capture.out);
    if [[ "$string" == *"npm install "* ]]; then
      npmAddCommand=$(echo ${string} | sed 's/.*\(npm install [a-zA-Z0-9.@^ -/]*\( \-\-save\(\-dev\)*\)*\).*/\1/');
      echo "Trying to automatically add package(s) by executing: ${npmAddCommand}";
      eval ${npmAddCommand};
      runNpmDev;
    fi
  fi
}

if command_exists yarn ; then
    yarn add vuetify &&
    yarn add sass sass-loader deepmerge vuetify-loader vue-property-decorator vue-class-component -D &&
    yarn install --force &&
    runYarnDev;
else
    npm install vuetify -P &&
    npm install  sass sass-loader deepmerge vuetify-loader vue-property-decorator vue-class-component -D &&
    npm install --force &&
    runNpmDev;
fi
