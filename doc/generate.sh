#!/bin/sh
set -e

SCRIPT_DIR=$(dirname -- "$0")
cd $SCRIPT_DIR

set -x
npx marked -i ../README.md -o public/index.html