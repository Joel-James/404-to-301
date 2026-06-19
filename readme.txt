=== 404 to 301 – Redirect Manager, 404 Error Logs & Notifications ===
Contributors: joelcj91, duckdev
Tags: redirect, redirection, redirect manager, 404, 404 error logs
Donate link: https://www.paypal.me/JoelCJ
Requires at least: 6.4
Tested up to: 7.0
Stable tag: 4.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom redirects (301, 302, 307), automatic 404 redirection, full 404 error logs and email alerts — a complete redirect & 404 toolkit.

== Description ==

**404 to 301** is a complete redirect manager and 404 error monitor for WordPress. Build your own custom redirects (301, 302, 307 and more) with exact, prefix or regex matching, automatically redirect every remaining 404 error to any page you choose, and keep a full log of every broken link that hits your site — so you can fix the real problem instead of just hiding it. An optional email alert lets you know the moment a URL starts getting hit.

Whether you are managing redirects after a site migration, cleaning up old URLs following a redesign, or simply protecting your SEO and visitor experience from dead links, 404 to 301 gives you precise redirect management **and** a 404 logging safety net for everything you miss — all from a fast, modern admin.

= Custom redirects =

Take full control of your URLs with a built-in redirect manager:

* Create unlimited **custom redirects** with your choice of redirect type (301, 302, 307 and more).
* Match URLs by **exact path, prefix or regular expression (regex)** for precise or pattern-based rules.
* Enable or disable individual redirects without deleting them.
* See a **hit counter and last-hit timestamp** on every redirect so you know what's actually being used.
* Manage everything from a full-featured table with search, filters, bulk actions and pagination.

= Automatic 404 redirection =

Don't have a custom rule for a broken URL? Set a **global fallback** and automatically redirect every leftover 404 error to your homepage, a custom page, or any URL — with the redirect type of your choice. No more dead-end 404 pages costing you visitors and link equity.

= 404 error logs =

Know exactly which links are breaking on your site:

* **Log every 404 error** with the requested URL, referrer, IP address, user agent and timestamp.
* Duplicate hits are **deduplicated and counted**, so a busy broken URL is one row with a hit count — not thousands.
* Track each error through a **lifecycle status** (open / ignored / fixed) and filter logs by date.
* Turn any logged 404 into a redirect in a couple of clicks.
* **GDPR-friendly:** IP addresses can be masked, and you can exclude paths you don't care about from logging.

= Email notifications =

Get an **email alert** when broken links appear, with a configurable hit threshold so busy sites don't flood your inbox. Stay on top of new 404s without living in the dashboard.

= Built for performance and developers =

* The plugin does work **only on a 404 request** — healthy page loads are never touched.
* Custom redirects are matched by a hashed, indexed lookup for near-instant resolution.
* **REST API** at `/404-to-301/v1/` for every admin operation.
* Full **WP-CLI** command set: `wp 404-to-301 logs|redirects|settings`.
* A filterable action pipeline so developers can hook in their own logic.
* **Multisite compatible** — each site keeps its own redirects and logs.

== Add-ons ==

Extend 404 to 301 with official add-ons. Browse the full catalogue at [https://duckdev.com/addons/404-to-301/](https://duckdev.com/addons/404-to-301/), or open the **Add-ons** tab inside the plugin.

= Free add-ons =

* [**Redirects Importer**](https://duckdev.com/addon/404-to-301-redirects-importer/) — Bulk import custom redirects into 404 to 301 from CSV files, or migrate them in from other redirect plugins like Redirection by John Godley and 301 Redirects – Redirect Manager by WebFactory — no manual re-entry.
* [**Logs Exporter**](https://duckdev.com/addon/404-to-301-logs-exporter/) — Export the 404 error log table as a downloadable CSV file directly from the Logs page.

= Premium add-ons =

* [**Logs Cleaner**](https://duckdev.com/addon/404-to-301-logs-cleaner/) — Auto-prune the 404 log table by age, by row count, or on a periodic schedule to keep your database lean.
* [**Email Reports**](https://duckdev.com/addon/404-to-301-email-reports/) — Periodic email reports — daily, weekly or monthly digests of your 404 activity, each with an attached CSV.
* [**Telegram Alerts**](https://duckdev.com/addon/404-to-301-telegram-alerts/) — Real-time Telegram alerts for 404 errors and redirects, delivered in the background so visitors never wait on the API call.

== Documentation & Support ==

* **Documentation:** [https://docs.duckdev.com/404-to-301/](https://docs.duckdev.com/404-to-301/)
* **Support forum:** [https://wordpress.org/support/plugin/404-to-301/](https://wordpress.org/support/plugin/404-to-301/)
* **Add-ons:** [https://duckdev.com/addons/404-to-301/](https://duckdev.com/addons/404-to-301/)

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/`, or install **404 to 301** directly from the WordPress.org plugin directory.
2. Activate it from the **Plugins** screen.
3. Open **404 to 301** in the admin sidebar — Logs, Redirects, Settings and Add-ons all live there.
4. Add your first custom redirect, or set a global 404 fallback under Settings, and you're done.

== Frequently Asked Questions ==

= Can I create my own custom redirects? =

Yes. The Redirects page lets you create unlimited custom redirects with exact, prefix or regex matching and your choice of redirect type (301, 302, 307 and more). Each redirect can be toggled active/inactive and shows a hit counter.

= What happens to 404 errors I don't have a redirect for? =

You can set a global fallback that automatically redirects every remaining 404 error to your homepage, a chosen page, or any URL — using the redirect type you prefer. If you'd rather leave them, every 404 is still logged so you can review and fix it.

= Does this slow down my site? =

No. The plugin only does work on a 404 request — normal, healthy page loads aren't touched at all. Custom redirects use a hashed, indexed lookup for near-instant matching.

= Can I import or export my redirects and logs? =

Yes, via add-ons. The free **Redirects Importer** bulk-imports redirects from CSV or other redirect plugins, the free **Logs Exporter** exports your 404 logs as a CSV file, and the premium **Email Reports** add-on emails periodic CSV digests of your 404 activity. See [the add-ons page](https://duckdev.com/addons/404-to-301/).

= Is it GDPR friendly? =

Yes. IP addresses in the 404 logs can be masked, and you can exclude specific paths from being logged altogether.

= Does it support multisite? =

Yes. Each site in the network keeps its own redirects and 404 logs.

= Where can I get help? =

Read the [documentation](https://docs.duckdev.com/404-to-301/) or post on the [support forum](https://wordpress.org/support/plugin/404-to-301/).

== Screenshots ==

1. General settings — core plugin options and behaviour.
2. Redirect settings — global 404 fallback and redirect defaults.
3. Log settings — 404 logging options, retention and exclusions.
4. Notification settings — email alerts on broken-link thresholds.
5. Tools — import, export and maintenance utilities.
6. 404 error logs list with filters, bulk actions, hit counts and lifecycle status.
7. Custom redirects manager with exact / prefix / regex matching and redirect types.

== Changelog ==

= 4.0.0 =
* New: Custom redirect manager with exact, prefix and regex matching and per-redirect redirect type.
* New: Active/inactive toggle, hit counter and last-hit timestamp on every redirect.
* New: Dedicated, indexed database tables for 404 logs and custom redirects.
* New: Modern React-powered admin with full-featured Logs and Redirects tables (search, filters, bulk actions, pagination).
* New: Per-log lifecycle status (open / ignored / fixed) and date filters.
* New: Email notifications with a configurable hit threshold.
* New: REST API at `/404-to-301/v1/`.
* New: WP-CLI command set — `wp 404-to-301 logs|redirects|settings`.
* New: Add-ons catalogue for free and premium extensions.
* Improve: IP masking and path exclusions for GDPR-friendly logging.

= 3.1.5 =
* Improve: Row action link.
* Fix: Unable to delete logs.

For the full release history, see the [changelog](https://docs.duckdev.com/404-to-301/changelog).

== Upgrade Notice ==

= 4.0.0 =
A major release with a brand-new redirect manager, modern admin, faster 404 logging, REST API and WP-CLI support. Back up your database before updating.
