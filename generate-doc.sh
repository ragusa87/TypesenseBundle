#!/bin/sh
set -e
SCRIPT_DIR=$(dirname -- "$0")
cd $SCRIPT_DIR

# Default action is "generate"
ARGS="$@"
if [ -z "$ARGS" ]; then
    ARGS="generate"
fi

function cleanup {
    rm -Rf ./static
    rm -f docs/README.md
    exit 1
}

# Cleanup previous doc
rm -Rf docs/public && mkdir -p docs/public

# Make sure we have an index
if [ ! -f ./docs/index.md ]; then
    cp -f README.md ./docs/index.md
fi

# Generate doc with daux.io
PORT=${PORT:-8085}
docker run --rm -it -w /build -p${PORT}:8085 -v "$PWD:/build" -u "$(id -u):$(id -g)" daux/daux.io daux $ARGS || (cleanup && exit 1)

mv ./static/* ./docs/public
cleanup
