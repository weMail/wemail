#!/bin/bash
# Build build/wemail-v<version>.zip for testing. Publishes nothing.
# Same file rules as bin/deploy.sh and .svnignore. Composer runs on the copy, so
# the local vendor/ keeps its dev packages.

set -e
cd "$(dirname "$0")/.."

VERSION=$(grep "Version: " wemail.php | awk '{ print $NF }')
ZIP="$PWD/build/wemail-v$VERSION.zip"
STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT

# Build with the Node version in .nvmrc when nvm is installed (NVM_DIR covers
# installs outside ~/.nvm, such as Herd's).
NVM_SH="${NVM_DIR:-$HOME/.nvm}/nvm.sh"
if [ -s "$NVM_SH" ]; then
    . "$NVM_SH"
    nvm install
fi

npm ci
npm run wp:build

rsync -r --exclude='*.git*' --exclude=bin --exclude=node_modules --exclude=build --exclude=.DS_Store --exclude=/vendor ./* "$STAGE/wemail"
composer install --no-dev --no-interaction --no-progress --working-dir="$STAGE/wemail"
find "$STAGE/wemail/vendor" -name '*.git*' -prune -exec rm -rf {} +
xargs -I{} rm -rf "$STAGE/wemail/{}" < .svnignore
rm -f "$STAGE/wemail/composer.json" "$STAGE/wemail/composer.lock"

rm -f "$ZIP"
(cd "$STAGE" && zip -qr "$ZIP" wemail)
echo "$ZIP"
