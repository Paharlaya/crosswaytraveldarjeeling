#!/bin/bash
# Fill in your real domain everywhere it's needed, in one go.
#
#   ./set-domain.sh crosswaydarjeeling.com
#
# Updates: canonical + og:url + og:image in all pages, the JSON-LD block,
# robots.txt, sitemap.xml, and writes the CNAME file GitHub Pages needs.

set -euo pipefail

if [ $# -ne 1 ]; then
    echo "usage: $0 yourdomain.com   (no https://, no trailing slash)" >&2
    exit 1
fi

DOMAIN="${1#http://}"; DOMAIN="${DOMAIN#https://}"; DOMAIN="${DOMAIN%/}"

FILES=$(grep -rl "REPLACE-WITH-YOUR-DOMAIN" . \
    --include="*.html" --include="*.txt" --include="*.xml" --include="*.webmanifest" 2>/dev/null || true)

if [ -z "$FILES" ]; then
    echo "Nothing to do — no REPLACE-WITH-YOUR-DOMAIN placeholders left."
else
    echo "$FILES" | while read -r f; do
        sed -i '' "s|REPLACE-WITH-YOUR-DOMAIN|$DOMAIN|g" "$f"
        echo "  updated $f"
    done
fi

printf '%s\n' "$DOMAIN" > CNAME
echo "  wrote CNAME -> $DOMAIN"

echo
echo "Done. Remaining manual steps:"
echo "  1. Point DNS at GitHub:"
echo "       A     @     185.199.108.153"
echo "       A     @     185.199.109.153"
echo "       A     @     185.199.110.153"
echo "       A     @     185.199.111.153"
echo "       CNAME www   <your-github-username>.github.io"
echo "  2. GitHub repo -> Settings -> Pages -> set the custom domain, tick Enforce HTTPS."
