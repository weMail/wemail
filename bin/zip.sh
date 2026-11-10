#!/bin/bash
# Build build/wemail-v<version>.zip for testing. Publishes nothing.
# Same file rules as bin/deploy.sh and .svnignore.

set -e
cd "$(dirname "$0")/.."

VERSION=$(grep "Version: " wemail.php | awk '{ print $NF }')
ZIP="$PWD/build/wemail-v$VERSION.zip"
STAGE=$(mktemp -d)
trap 'rm -rf "$STAGE"' EXIT

rsync -r --exclude='*.git*' --exclude=bin --exclude=node_modules --exclude=build --exclude=.DS_Store ./* "$STAGE/wemail"
xargs -I{} rm -rf "$STAGE/wemail/{}" < .svnignore

rm -f "$ZIP"
(cd "$STAGE" && zip -qr "$ZIP" wemail)
echo "$ZIP"
