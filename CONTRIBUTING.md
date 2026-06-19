# Contributing to 404 to 301

Thanks for taking the time to contribute! **404 to 301** is a redirect
manager and 404 error monitor for WordPress, and contributions of all
kinds — bug reports, feature ideas, documentation, and code — are
welcome.

This document explains how to get a development copy running, the
standards your changes are expected to meet, and how to get them
reviewed.

## Ways to contribute

- **Report a bug** — open an [issue](https://github.com/Joel-James/404-to-301/issues)
  with clear reproduction steps, the WordPress and PHP versions, and the
  plugin version.
- **Suggest a feature** — open an issue describing the problem you want
  solved (not just the solution you have in mind).
- **Ask a support question** — please use the
  [WordPress.org support forum](https://wordpress.org/support/plugin/404-to-301/)
  rather than the issue tracker.
- **Send a pull request** — see [Development](#development) below.

For anything touching security, **do not open a public issue** — follow
[SECURITY.md](SECURITY.md) instead.

## Development

### Requirements

- PHP **7.4+** (the codebase and CI target 7.4 as the floor and test up
  to 8.3).
- [Composer](https://getcomposer.org/) for PHP dependencies and tooling.
- [Node.js](https://nodejs.org/) (LTS) and npm for the admin UI build.
- A local WordPress install — running the plugin from inside
  `wp-content/plugins/404-to-301` is the simplest setup.

### Getting started

```bash
# Clone into your wp-content/plugins directory.
git clone git@github.com:Joel-James/404-to-301.git
cd 404-to-301

# Install PHP dependencies and dev tooling.
composer install

# Install JS dependencies and build the admin assets.
npm install
npm run build
```

While working on the React admin UI, use `npm run start` for a watch
build instead of `npm run build`.

## Coding standards

PHP follows the [WordPress Coding Standards](https://github.com/WordPress/WordPress-Coding-Standards)
plus [PHPCompatibility](https://github.com/PHPCompatibility/PHPCompatibilityWP),
enforced by the ruleset in `phpcs.xml.dist`.

```bash
# Lint PHP.
composer lint

# Auto-fix what can be fixed automatically.
composer lint:fix

# Lint and format the JavaScript / React sources.
npm run lint:js
npm run format
```

A few conventions worth knowing:

- Files declare `strict_types` and live under the
  `DuckDev\FourNotFour` namespace.
- Every class, method, and hook carries a docblock; match the density
  and tone of the surrounding code.
- New user-facing strings must be translatable (text domain
  `404-to-301`).

## Tests

PHPUnit covers the models, migration, API, and front-end matching logic.

```bash
composer test
```

CI runs PHPCS and the PHPUnit matrix on every push and pull request.
Please add or update tests for any behaviour you change, and make sure
the suite passes locally before opening a PR.

## Branching & pull requests

The default development branch is `dev`; `master` tracks released code.
Branch off `dev` using the prefixes CI already recognises:

- `new/<short-description>` — new features
- `fix/<short-description>` — bug fixes
- `improve/<short-description>` — refactors and improvements

When opening a pull request:

1. Target the `dev` branch.
2. Keep the PR focused on a single concern; split unrelated changes.
3. Write a clear description of **what** changed and **why**.
4. Make sure `composer lint` and `composer test` pass.
5. Update `readme.txt` / documentation if behaviour changed.

Commits are authored solely by the contributor — please don't add
co-author trailers for tooling.

## License

By contributing, you agree that your contributions are licensed under
the [GPL-2.0-or-later](LICENSE) license that covers the project.
