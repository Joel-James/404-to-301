# 404 to 301

[![CI](https://github.com/Joel-James/404-to-301/actions/workflows/ci.yml/badge.svg)](https://github.com/Joel-James/404-to-301/actions/workflows/ci.yml)
[![License: GPL-2.0+](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](LICENSE)

Automatically redirect every 404 error to any page using a 301 redirect, log every 404 request, and get email notifications when broken links are hit. Built for SEO.

> v4 is a ground-up rewrite — OOP-first, React-powered admin, BerlinDB-backed tables, REST API and WP-CLI commands.

---

## Features

- **Custom redirects** — exact / prefix / regex matches, per-row redirect status, active/inactive toggle and per-row hit counters.
- **404 logs** — every 404 is logged once and de-duplicated, with a `hits` counter, ignored/fixed lifecycle and date filters.
- **Email notifications** — alert on N-th hit so the inbox doesn't get hammered by busy sites.
- **React admin UI** — built on `@wordpress/components` + `@wordpress/dataviews`, mirrors the LLC reference plugin layout.
- **REST API** — every list / CRUD operation has a clean REST endpoint at `/404-to-301/v1/`.
- **WP-CLI** — full `wp 404-to-301` command surface (`logs`, `redirects`, `settings`, `migrate`).
- **Background migration** — v3 → v4 data migration runs in chunks via wp-cron, or opportunistically via Action Scheduler when it's available.
- **Add-on catalogue + Freemius licensing** — addons grid with license-key activate/deactivate.

---

## Requirements

- WordPress **6.4** or later
- PHP **7.4** or later
- MySQL 5.6 / MariaDB 10.1 or later

---

## Folder layout

```
404-to-301/
├── 404-to-301.php             # bootstrap (constants, autoload, plugins_loaded boot)
├── uninstall.php              # drops options + tables on plugin delete
├── includes/                  # all PHP
│   ├── class-core.php         # final Core extends Singleton
│   ├── class-plugin.php       # SLUG, PAGE_*, screen ids, urls
│   ├── class-settings.php     # option-backed settings + REST
│   ├── class-freemius.php     # licensing wrapper
│   ├── admin/                 # Menu, Assets, Page, Links
│   ├── api/                   # REST endpoints
│   ├── database/              # BerlinDB schemas/tables/queries/rows
│   ├── models/                # facades the rest of the plugin talks to
│   ├── front/                 # Controller + Request + Actions
│   ├── migration/             # v3 → v4 chunked migrator
│   ├── cli/                   # WP-CLI subcommands
│   ├── contracts/             # Routable, Runnable, Actionable interfaces
│   ├── setup/                 # Activator, Deactivator, Upgrader
│   └── utils/                 # Singleton, Permission, Helpers, Sanitizer, Assets
├── templates/                 # PHP mount points the admin React apps attach to
├── assets/src/                # React + SCSS sources
│   ├── settings.js logs.js redirects.js addons.js  # entry files
│   ├── common/                # PageHeader, PageBody, TabNav, Footer, Notices
│   ├── hooks/                 # use-settings, use-logs, use-redirects, ...
│   ├── modules/               # one folder per page: settings/, logs/, ...
│   └── styles/                # shared.scss + per-page partials
├── build/                     # wp-scripts output (gitignored)
├── bin/                       # install-wp-tests.sh, pack.sh
├── tests/phpunit/             # PHPUnit tests
├── languages/                 # POT + translations
└── .github/workflows/         # ci.yml + release.yml
```

---

## Development

Install dependencies:

```bash
composer install
npm install
```

Build the React bundles:

```bash
npm run build         # one-off build
npm start             # watch mode
```

Run the linters / tests:

```bash
composer lint         # PHPCS (WordPress + WordPress-Extra + PHPCompatibility 7.4+)
composer lint:fix     # PHPCBF auto-fix
composer test         # PHPUnit (requires bin/install-wp-tests.sh once)

npm run lint:js
npm run format
```

Pack a release-ready ZIP into `releases/`:

```bash
npm run pack
```

`bin/pack.sh` verifies that the version header in `404-to-301.php`, `package.json` and `readme.txt` all match, then regenerates the POT, builds the assets, installs production-only Composer deps and stages the runtime files into a `404-to-301-<version>.zip`.

---

## REST API

All routes live under `/wp-json/404-to-301/v1/` and require the plugin's `manage` capability (`manage_options` by default, filterable via `404_to_301_capability`).

| Route                       | Methods                | Notes                                                   |
|-----------------------------|------------------------|---------------------------------------------------------|
| `/logs`                     | `GET`, `DELETE`        | List + bulk delete                                      |
| `/logs/{id}`                | `GET`, `PUT`, `DELETE` | Single log: read, set status / link redirect, delete    |
| `/redirects`                | `GET`, `POST`, `DELETE`| List + create + bulk delete                             |
| `/redirects/{id}`           | `GET`, `PUT`, `DELETE` | Single redirect CRUD                                    |
| `/addons`                   | `GET`                  | Addon catalogue (Freemius when configured, else stub)   |
| `/addons/license`           | `POST`, `DELETE`       | Activate / deactivate a license key                     |
| `/migration`                | `GET`, `POST`, `DELETE`| Migration status, start phase 2, abort                  |

Settings live on the core `/wp/v2/settings` endpoint via `show_in_rest`, exactly the LLC pattern.

---

## WP-CLI

Run `wp help 404-to-301` for the full reference; here are the highlights:

```bash
# Logs
wp 404-to-301 logs list [--status=] [--search=] [--per-page=] [--format=]
wp 404-to-301 logs get <id>
wp 404-to-301 logs status <id> --to=open|ignored|fixed
wp 404-to-301 logs delete <id>... | --all | --status=
wp 404-to-301 logs prune [--days=30]

# Redirects
wp 404-to-301 redirects list [--match-type=] [--active] [--per-page=]
wp 404-to-301 redirects get <id>
wp 404-to-301 redirects create --source=/old --target=https://example.com [--type=301] [--match-type=exact]
wp 404-to-301 redirects update <id> [--target=] [--type=] [--active=]
wp 404-to-301 redirects delete <id>... | --all

# Settings
wp 404-to-301 settings get [<key>]
wp 404-to-301 settings update <key> <value>     # JSON-decoded when possible
wp 404-to-301 settings reset [--yes]

# Migration
wp 404-to-301 migrate status
wp 404-to-301 migrate run [--limit=N] [--phase=1|2|all]
wp 404-to-301 migrate abort
```

---

## Migration from v3

When v4 first boots on a site with the legacy `wp_404_to_301` table:

1. **Phase 1 (automatic)** — rows that carry a custom redirect are migrated into the new `404_to_301_redirects` table. Runs unattended on activation.
2. **Phase 2 (opt-in)** — the Logs page shows a banner with the number of remaining legacy log rows and a "Start migration" button. The chunked migrator runs in the background (via wp-cron, or via Action Scheduler when present) until the legacy table is empty, then drops it and removes the legacy options.

Don't have Action Scheduler? You don't need it. The banner offers a one-click "Install Action Scheduler" link that hands off to WordPress's own plugin installer for users with `install_plugins`, but the chunked fallback works on every host.

---

## Hooks

The full hook list is documented inline next to each call. Notable ones:

- `404_to_301_init`                  — fires once `Core` has booted.
- `404_to_301_request` / `_404_request`  — fired after the front-end action chain.
- `404_to_301_actions`               — filter the action chain (Redirect/Log/Email).
- `404_to_301_settings_defaults`     — register addon settings.
- `404_to_301_settings_pre_update`   — pre-write hook for the settings option.
- `404_to_301_capability` / `_has_access` — change who can manage the plugin.
- `404_to_301_redirect_target`       — last-mile override of a resolved redirect.
- `404_to_301_addons_catalog`        — replace / extend the addons grid.
- `404_to_301_migration_complete`    — fires when v3 → v4 migration finishes.

---

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

Maintained by [Joel James](https://duckdev.com/) at DuckDev.
