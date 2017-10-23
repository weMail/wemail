#!/bin/bash

mkdir -p assets/js;
cat assets/src/vendor/bootstrap/js/util.js \
    assets/src/vendor/bootstrap/js/dropdown.js \
    assets/src/vendor/bootstrap/js/modal.js | \
    shx sed "s/^(import|export).*//" | \
    babel --no-babelrc --presets=es2015 --filename assets/src/vendor/bootstrap/js/bootstrap.js | \
    node assets/src/vendor/bootstrap/build/stamp.js > assets/js/bootstrap.js
