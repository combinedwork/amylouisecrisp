#!/usr/bin/env bash
# Copies the shared design files into the WordPress theme and makes the two upload zips.
# Run from the repository root:  bash wordpress/build.sh
set -euo pipefail
cd "$(dirname "$0")/.."
for f in styles.css site.js consent.js recipe-card.js home.js recipes-page.js recipe-page.js; do
  cp "$f" wordpress/amy-louise-crisp/assets/
done
mkdir -p wordpress/dist
rm -f wordpress/dist/amy-louise-crisp-theme.zip wordpress/dist/amy-recipes-plugin.zip
(cd wordpress && zip -qr dist/amy-louise-crisp-theme.zip amy-louise-crisp -x '*.DS_Store')
(cd wordpress && zip -qr dist/amy-recipes-plugin.zip amy-recipes -x '*.DS_Store')
ls -lh wordpress/dist
