#!/usr/bin/env sh

cmd="/usr/bin/env phpmd --exclude vendor,tests/tpm"
args="text phpmd.xml.dist"

if [ "$#" -ne 0 ]; then
$cmd "$@" $args
else 
$cmd . $args
fi

