#!/bin/sh
echo -ne '\033c\033]0;StarDeception\a'
base_path="$(dirname "$(realpath "$0")")"
"$base_path/StarDeception.dedicated_server.x86_64" "$@"
