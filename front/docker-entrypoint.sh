#!/bin/sh
set -e

replace_runtime_env() {
  placeholder=$1
  value=$2

  if [ -n "$value" ]; then
    grep -rl "$placeholder" ./.next ./public 2>/dev/null | while read -r file; do
      sed -i "s|$placeholder|$value|g" "$file"
    done
  fi
}

replace_runtime_env "__RUNTIME_NEXT_PUBLIC_API_URL__" "$NEXT_PUBLIC_API_URL"
replace_runtime_env "__RUNTIME_NEXT_PUBLIC_MERCURE_URL__" "$NEXT_PUBLIC_MERCURE_URL"

exec "$@"
