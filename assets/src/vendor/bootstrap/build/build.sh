#!/bin/bash

mkdir -p assets/js;
cat assets/src/vendor/bootstrap/js/util.js \
    assets/src/vendor/bootstrap/js/dropdown.js \
    assets/src/vendor/bootstrap/js/modal.js | \
    shx sed "s/^(import|export).*//" | \
    shx sed "s/bsTransitionEnd/wemailTransitionEnd/" | \
    shx sed "s/bs\./wemail\./" | \
    shx sed "s/const NAME                         = 'modal'/const NAME                         = 'wemailModal'/" | \
    shx sed "s/\"modal\"/\"wemail-modal\"/" | \
    shx sed "s/modal-/wemail-modal-/" | \
    shx sed "s/const NAME                         = 'dropdown'/const NAME                         = 'wemailDropdown'/" | \
    shx sed "s/\"dropdown\"/\"wemail-dropdown\"/" | \
    shx sed "s/dropdown-/wemail-dropdown-/" | \
    babel --no-babelrc --presets=es2015 --filename assets/src/vendor/bootstrap/js/bootstrap.js | \
    node assets/src/vendor/bootstrap/build/stamp.js > assets/js/bootstrap.js
