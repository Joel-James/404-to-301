#!/usr/bin/env bash
#
# Guard against new `__experimental*` *component* imports from the
# @wordpress packages in the admin React app.
#
# Why this exists: these exports are not guaranteed to be present in the
# `@wordpress/components` version that WordPress bundles on a given site.
# When one is missing the imported binding is `undefined`, rendering it
# throws, and — before the AppShell ErrorBoundary — that blanked the
# whole admin page (this bit the v3->v4 migration banner via
# `__experimentalText`). The ErrorBoundary now degrades such a throw to
# an inline notice, but every experimental component dependency should
# still be a deliberate, reviewed choice rather than something that
# silently creeps in.
#
# How it works: every aliased experimental import (`__experimentalX as X`)
# under assets/src is compared against the allowlist in
# `bin/experimental-imports.allow`. Adding a new one fails the check
# until it's added to the allowlist; removing one fails until it's
# dropped, so the list stays honest. JSX *props* (e.g.
# `__experimentalValidateInput` passed to a component) are not matched —
# only the aliased imports, which is how components are pulled in.

set -euo pipefail

cd "$(dirname "$0")/.."

allow="bin/experimental-imports.allow"

if [ ! -f "$allow" ]; then
	echo "Missing allowlist: $allow" >&2
	exit 1
fi

current="$(grep -rEoh "__experimental[A-Za-z0-9]+ as [A-Za-z0-9]+" assets/src --include="*.js" | sort -u || true)"
expected="$(grep -v '^[[:space:]]*$' "$allow" | sort -u)"

if [ "$current" != "$expected" ]; then
	echo "Experimental @wordpress component imports differ from the allowlist." >&2
	echo >&2
	echo "  < expected (in $allow)   > found (in assets/src)" >&2
	echo >&2
	diff <(printf '%s\n' "$expected") <(printf '%s\n' "$current") >&2 || true
	echo >&2
	echo "If you intentionally added an experimental import, append it to" >&2
	echo "$allow and make sure it renders inside the AppShell ErrorBoundary." >&2
	echo "If you removed one, delete it from the allowlist to keep this current." >&2
	exit 1
fi

echo "Experimental import allowlist is up to date ($(printf '%s\n' "$current" | grep -c . ) tracked)."
