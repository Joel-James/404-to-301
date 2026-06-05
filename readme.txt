=== 404 to 301 - Redirect, Log and Notify 404 Errors ===
Contributors: joelcj91, duckdev
Tags: 404, 301, 302, 307, redirect, not found, 404 redirect, custom 404 page, seo redirect, broken links
Donate link: https://www.paypal.me/JoelCJ
Requires at least: 6.4
Tested up to: 6.5
Stable tag: 4.0.0
Requires PHP: 7.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically redirect every 404 error to any page using a 301 redirect, log every 404 request, and get email notifications when broken links are hit. Built for SEO.

== Description ==

If you care about your site, you care about 404 errors — they hurt the visitor experience and your SEO. 404 to 301 finds them, logs them, lets you fix them with one click, and quietly redirects everything to a sensible destination.

= What's new in v4 =

v4 is a ground-up rewrite focused on three things:

* **A modern admin** — built on the React components and DataView library that ship with WordPress core, the same toolkit Gutenberg uses. Logs and Redirects get full-feature tables with filters, bulk actions, pagination and live search.
* **A proper data model** — error logs and custom redirects now live in two dedicated, indexed database tables instead of one big shared one. URLs are hashed for instant lookup; IPs are stored compactly via `inet_pton`; redirects support exact, prefix and regex matching.
* **Hooks, REST and WP-CLI** — every action runs through a filterable pipeline, every admin operation has a matching REST endpoint at `/404-to-301/v1/`, and there's a full `wp 404-to-301 ...` command set for automation.

= Features =

* **Custom redirects** with exact, prefix or regex matching, per-row redirect status (301/302/307), active/inactive flag, hit counter, last-hit timestamp.
* **404 logs** with deduplication, hit counter, lifecycle status (open / ignored / fixed) and date filters.
* **Email notifications** with a configurable hit threshold so busy sites don't flood the inbox.
* **Site Health-friendly** — `redirect_canonical` is opt-in disabled, IP can be masked for GDPR.
* **Exclude paths** — skip 404s on paths you don't care about.
* **WP-CLI commands** for logs, redirects, settings and migration.
* **REST API** at `/404-to-301/v1/`.
* **Add-on catalogue** with license-key activation via Freemius.
* **Background migration** from v3 — runs in chunks, opportunistically uses Action Scheduler when available.

== Installation ==

1. Upload the plugin folder to `/wp-content/plugins/` (or install it from the WordPress.org directory).
2. Activate it from the **Plugins** screen.
3. Open **404 to 301** in the admin sidebar — Logs, Redirects, Settings and Add-ons live there.

= Upgrading from v3 =

The first time v4 boots on an existing install, any custom redirects you had configured on the old logs table are migrated automatically. A banner on the Logs page then lets you start a chunked migration of the remaining log rows. The legacy table is dropped once the migration completes.

== Frequently Asked Questions ==

= Does this slow down my site? =

No. The plugin only does any work on a 404 request — healthy page loads aren't touched at all.

= How does the redirect lookup work? =

The 404 URL is normalised (lowercased, trailing slash + query string stripped), hashed via SHA1, and matched against a unique-keyed column. That's an O(1) lookup per request. Prefix and regex rules are walked in memory after the hash check misses.

= Can I add my own actions to the 404 pipeline? =

Yes — every action class implements the `Actionable` interface and the chain is filterable via `404_to_301_actions`. Your action just needs a `run(Request $request)` method.

= Does this support multisite? =

Yes — the BerlinDB-backed tables are per-site, so each site keeps its own logs and redirects.

== Screenshots ==

1. 404 logs page with filters, bulk actions and lifecycle status.
2. Custom redirects with exact / prefix / regex matching.
3. Settings page split across General, Redirects, Logs, Notifications and Tools tabs.

== Changelog ==

= 4.0.0 =
* Ground-up rewrite with OOP architecture under the `DuckDev\FourNotFour` namespace.
* React-powered admin UI on `@wordpress/dataviews` and `@wordpress/components`.
* New BerlinDB-backed `404_to_301_logs` and `404_to_301_redirects` tables, with automatic migration from the v3 schema.
* New REST API at `/404-to-301/v1/`.
* New WP-CLI command set: `wp 404-to-301 logs|redirects|settings|migrate`.
* Per-redirect lifecycle (active/inactive, exact/prefix/regex match) and hit counter.
* Email threshold setting so busy sites don't get flooded.
* Optional Action Scheduler integration for background migration.
* Add-on catalogue + Freemius licensing.
* Drops Symfony HttpFoundation, jQuery / thickbox modals and the legacy `JJ4T3_*` class surface.

= 3.1.3 =
* Compatibility update for WP 5.8.

== Upgrade Notice ==

= 4.0.0 =
Major rewrite — back up your database before upgrading. The plugin migrates your existing custom redirects automatically and offers a one-click background migration for your existing log rows. The v3 `JJ4T3_*` class surface and `jj4t3_*` hook prefix have been retired; if you wrote custom add-ons against them, see README.md for the new equivalents.
