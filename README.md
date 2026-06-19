# 404 to 301

[![CI](https://github.com/Joel-James/404-to-301/actions/workflows/ci.yml/badge.svg)](https://github.com/Joel-James/404-to-301/actions/workflows/ci.yml)
[![License: GPL-2.0+](https://img.shields.io/badge/license-GPL--2.0%2B-blue.svg)](LICENSE)

Automatically redirect every 404 error to any page using a 301 redirect, log every 404 request, and get email notifications when broken links are hit. Built for SEO.

> v4 is a ground-up rewrite — OOP-first, React-powered admin, BerlinDB-backed tables, REST API and WP-CLI commands.

**📖 Full documentation: [docs.duckdev.com/404-to-301](https://docs.duckdev.com/404-to-301/getting-started)**

---

## Features

- **Custom redirects** — exact / prefix / regex matches, per-row redirect status, active/inactive toggle and per-row hit counters.
- **404 logs** — every 404 is logged once and de-duplicated, with a `hits` counter, ignored/fixed lifecycle and date filters.
- **Email notifications** — alert on N-th hit so the inbox doesn't get hammered by busy sites.
- **React admin UI** — built on `@wordpress/components` + `@wordpress/dataviews`.
- **REST API** — every list / CRUD operation has a clean endpoint under `/404-to-301/v1/`.
- **WP-CLI** — full `wp 404-to-301` command surface (`logs`, `redirects`, `settings`, `migrate`).
- **Background migration** — v3 → v4 data migration runs in chunks via wp-cron, or via Action Scheduler when available.
- **Add-on catalogue + Freemius licensing** — addons grid with license-key activation.

---

## Requirements

- WordPress **6.4** or later
- PHP **7.4** or later
- MySQL 5.6 / MariaDB 10.1 or later

---

## Documentation

- [Getting started & settings](https://docs.duckdev.com/404-to-301/getting-started)
- [Match modes & query handling](https://docs.duckdev.com/404-to-301/redirects/matching)
- [Developer docs (hooks & REST API)](https://docs.duckdev.com/404-to-301/developer-docs)
- [WP-CLI commands](https://docs.duckdev.com/404-to-301/wp-cli)
- [Add-ons](https://docs.duckdev.com/404-to-301/addons/)

---

## Development

See [CONTRIBUTING.md](CONTRIBUTING.md) for environment setup, build commands, coding standards and how to run the linters and tests.

### Packaging a release

```bash
npm run pack
```

`bin/pack.sh` verifies that the version header in `404-to-301.php`, `package.json` and `readme.txt` all match, then regenerates the POT, builds the assets, installs production-only Composer deps and stages the runtime files into a `404-to-301-<version>.zip` under `releases/`.

---

## Contributing

Bug reports, feature ideas and pull requests are welcome. Please read [CONTRIBUTING.md](CONTRIBUTING.md) before opening a PR.

## Security

Found a vulnerability? **Don't open a public issue.** See [SECURITY.md](SECURITY.md) for how to report it privately.

## Support

- Documentation — [docs.duckdev.com/404-to-301](https://docs.duckdev.com/404-to-301/getting-started)
- Support forum — [wordpress.org/support/plugin/404-to-301](https://wordpress.org/support/plugin/404-to-301/)
- Bugs & features — [GitHub issues](https://github.com/Joel-James/404-to-301/issues)

---

## License

GPL-2.0-or-later. See [LICENSE](LICENSE).

Maintained by [Joel James](https://duckdev.com/) at DuckDev.
