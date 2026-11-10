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

npm ci
NODE_OPTIONS=--openssl-legacy-provider npm run wp:build

rsync -r --exclude='*.git*' --exclude=bin --exclude=node_modules --exclude=build --exclude=.DS_Store --exclude=/vendor ./* "$STAGE/wemail"
composer install --no-dev --no-interaction --no-progress --working-dir="$STAGE/wemail"
find "$STAGE/wemail/vendor" -name '*.git*' -prune -exec rm -rf {} +
xargs -I{} rm -rf "$STAGE/wemail/{}" < .svnignore
rm -f "$STAGE/wemail/composer.json" "$STAGE/wemail/composer.lock"

rm -f "$ZIP"
(cd "$STAGE" && zip -qr "$ZIP" wemail)
echo "$ZIP"
