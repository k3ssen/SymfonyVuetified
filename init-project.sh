#!/bin/bash

command_exists () {
    type "$1" &> /dev/null ;
}

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
    yarn install &&
    runYarnDev;
else
    npm install &&
    runNpmDev;
fi
