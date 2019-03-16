=== 404 to 301 ===
Contributors: joelcj91,duckdev
Tags: 404, 301, 302, 307, not found, 404 redirect, 404 to 301, 301 redirect, seo redirect, error redirect, 404 seo, custom 404 page
Donate link: https://www.paypal.me/JoelCJ
Requires at least: 3.5
Tested up to: 5.1
Stable tag: 3.0.4
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Automatically redirect, log and notify all 404 page errors to any page using 301 redirect for SEO. No more 404 Errors in WebMaster tool.

== Description ==

If you care about your website, you should take steps to avoid 404 errors as it affects your SEO badly. 404 ( Page not found ) errors are common and we all hate it, even Search engines do the same! Install this plugin then sit back and relax. It will take care of 404 errors!

= What is 404 to 301? =

*Handling 404 errors in your site should be easy. With this plugin, it finally is.*

> #### 404 to 301 Log Manager - Add-on is now available!
>
> - Instead of instant email alerts, get **hourly, twice daily, daily, twice weekly, weekly** alerts.<br />
> - Limit the amount of emails sent out based on error logs count.<br />
> - **PDF file** attachment of error logs will be delivered through the email.<br />
> - **Automatically clear** old error logs based on time period.<br />
> - Get email alerts to multiple email recipients.<br />
>
> [Get this add-on now](https://duckdev.com/products/404-to-301-log-manager/) | [See Docs](https://duckdev.com/support/docs/404-to-301-log-manager/)

404 to 301 is a simple but amazing plugin which handles all 404 errors for you. It will redirect all 404 errors to any page that you set, using 301 (or any other) status. That means no more 404 errors! Even in Google webmaster tool you are safe!
You will not see any 404 error reports in your webmaster tool dashboard.


> #### 404 to 301 - Features
>
> - You can redirect errors to any existing page or custom link (globally).<br />
> - **You can set custom redirect for each 404 path!**<br />
> - No more 404 errors in your website. Seriously!<br />
> - **Translation ready!**<br />
> - You can optionally monitor/log all errors.<br />
> - Exclude paths from errors.<br />
> - You can optionally enable email notification on all 404 errors.<br />
> - You can choose which redirect method to be used (301,302,307).<br />
> - Will not irritate your visitors if they land on a non-existing page/url.<br />
> - Increase your SEO by telling Google that all 404 pages are moved to some other page.<br />
> - Completely free to use with lifetime updates.<br />
> - Developer friendly.<br />
> - Follows best WordPress coding standards.<br />
> - Of course, available in [GitHub](https://github.com/joel-james/404-to-301)<br />
>
> [Installation](https://wordpress.org/plugins/404-to-301/installation/) | [Docs](https://duckdev.com/support/docs/404-to-301/) | [Screenshots](https://wordpress.org/plugins/404-to-301/screenshots/)


**Bug Reports**

Bug reports for 404 to 301 are always welcome. [Report here](https://duckdev.com/support/).

**More information**

- [404 to 301 - Plugin Homepage](https://duckdev.com/products/404-to-301), containing more details and docs.
- Follow the developer [@Twitter](https://twitter.com/Joel_James)
- Other [WordPress plugins](https://profiles.wordpress.org/joelcj91/#content-plugins) by Joel James for [Duck Dev](https://duckdev.com)

**404 Errors and Redirect - More Details**

If you are confused with these terms 404,301, redirect etc, [refer this page](https://moz.com/learn/seo/redirection/) to know more about the redirect and SEO.


== Installation ==


= Installing the plugin - Simple =
1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **404 to 301** and click "*Install now*"
2. Alternatively, download the plugin and upload the contents of `404-to-301.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
3. Activate the plugin
4. Go to 404 to 301 tab on your admin menus.
5. Configure the plugin options with available settings.


= Need more help? =
Please take a look at the [plugin documentation](https://duckdev.com/support/docs/404-to-301/) or [open a support request](http://wordpress.org/support/plugin/404-to-301/).

= Missing something? =
If you would like to have an additional feature for this plugin, [let me know](https://duckdev.com/support/)

== Frequently Asked Questions ==

= What is the use of 404 to 301? =

It will increase your SEO by redirecting all 404 errors using SEO redirects.

= Can I monitor 404 errors? =

Yes. You can. If you enable logs from settings, it will list all the errors.

= How can I clear logs? =

Select 'clear logs' from bulk actions and submit. It will delete all log data from your db.

= Can I get email notifications? =

Yes. You can enable email notifications on each 404 errors (optional).

= Can I set custom redirects for each errors? =

Yes. You can set that from error logs table.

= I need more details =

Please take a look at the [plugin documentation](https://duckdev.com/support/docs/404-to-301/) or [open a support request](http://wordpress.org/support/plugin/404-to-301/).


== Other Notes ==

= Bug Reports =

Bug reports for 404 to 301 are always welcome. [Report here](https://duckdev.com/support/).


== Screenshots ==

1. **Settings** - Settings page of 404 to 301.
2. **Error Logs** - Logged 404 Errors.
3. **Custom Redirect** - Setting custom redirect for each 404 paths.


== Changelog ==

= 3.0.4 (16/03/2019) =
**📦 New**

* Added option to disable URL guessing.
* Added review notice.

= 3.0.3 (15/03/2019) =
**🐛 Bug Fixes**

* Opt-in is disabled temporarily to debug the issues.

= 3.0.2 (26/02/2019) =
**🐛 Bug Fixes**

* Security fix.

**👌 Improvements**

* Minor performance improvements.

= 3.0.1 (24/08/2018) =
**👌 Improvements**

* Make release automated.

**🐛 Bug Fixes**

* Do not include exclude path items.

= 3.0.0.1 (25/06/2018) =
**Bug Fixes**

- Using template_redirect hook for redirect instead of wp hook.
- Fixed an issue with do_action in Freemius SDK.

= 3.0.0 (20/06/2018) =
**New Features**

- Individual optional settings for each error log item (Individual redirec, log, email alert can be set).
- Clear error logs without removing custom redirects.
- Added error logs grouping with count.
- [WPML compatible](https://wpml.org/plugin/404-to-301/).
- Integrated Freemius for addon, support and analytics (optional).

**Improvements**

- Complete code revamp. More improved structure.
- Set custom options from previous logs if same item exists.
- Made 3rd party integration easier.

= 2.3.3 (31/08/2016) =
**Bug Fixes**

- Using esc_url() for Ref and Url fields.
- Fixed Cross Site Scripting vulnerability in "From" column - Thanks to [Plugin Vulnerabilities](https://www.pluginvulnerabilities.com/).

= 2.3.1 (27/08/2016) =
**Bug Fixes**

- Fixed Cross Site Scripting vulnerability - Thanks to [Summer of Pwnage](https://www.sumofpwn.nl/) & Louis Dion-Marcil.
- Fixed sorting issue in error log (Changed default order to Date Descending order).
- Fixed issues when trailing slash found at the end of custom redirect.

**Improvements**

- Tested with WordPress 4.6.

= 2.3.0 (17/08/2016) =
**Bug Fixes**

- Removed unused UAN button from help page.
- Completely safe to use.
- Tracking completely removed from the plugin since it was detected as spam. Read more [here](https://duckdev.com/blog/404-to-301-plugin-detected-by-wordfence-here-is-what-actually-happened/).

= 2.2.9 (16/08/2016) =
**Bug Fixes**

- Serious issue fixed - Usage tracking script was being detected as spam.
- Removed tracking completely.

= 2.2.8 (12/07/2016) =
**Bug Fixes**

- Fixed a minor bug on TOC button.

= 2.2.7 (07/07/2016) =
**Bug Fixes**

- Fixed issue with PHP 5.4 - Empty error log data.

**Improvements**

- Improved condition checking.
- Speed improvements.
- Made error log link to new tab.

= 2.2.6 (30/06/2016) =
**Bug Fixes**

- Fixed issue - Undefined index when accessed directly.

**Improvements**

- Improved condition checking.

= 2.2.5 (05/06/2016) =
**Bug Fixes**

- Fixed issue - Front end was slow.

= 2.2.4 (02/06/2016) =
**Bug Fixes**

- Fixed custom redirect issue.
- Fixed issues when activating.

= 2.2.2 (01/06/2016) =
**New Feature**

- Now you can set **custom redirects** for reach error path.
- Goto error logs list and set custom redirect.
- Fixed issues with BuddyPress.

**Improvements**

- Improved code.

= 2.1.7 (20/04/2016) =
**New Add-on**

- New [Log Manager](https://duckdev.com/products/404-to-301-log-manager/) add-on available now.
- Get periodic email alerts instead of instant email alerts for every errors (add-on).
- Automatically clear error logs (add-on).

**Improvements**

- Removed inactive filter - i4t3_before_404_redirect

= 2.1.6 (06/04/2016) =
**Improvements**

- Fixed broken plugin website links.
- Tested with WordPress 4.5.

= 2.1.5 (22/03/2016) =
**Improvements**

- Fixed issues with deprecated functions - Thanks to [Pedro Mendonça](https://github.com/pedro-mendonca).
- Translated missing strings.
- Tested with WordPress 4.4.2.

= 2.1.4 (22/01/2016) =
**Bug Fixes**

- Fixed issues when clearing logs (header already sent..).
- Tested with WordPress 4.4.1.

= 2.1.3 (20/12/2015) =
**Bug Fixes**

- Fixed issues with older version of WordPress.
- Fixed issues with older version of PHP.

= 2.1.0 (20/12/2015) =
**New Feature**

- New option to set items per page from error log listing page.
- New option to show or hide items from listing table (Screen option).

**Improvements**

- Improved error listing page table structure.

**Bug Fixes**

- Fixed issue - Null value issue when no Referrer or User Agent found.
- Fixed issue - Clearing errors and redirecting.

= 2.0.9 (2/11/2015) =
**Bug Fixes**

- Fixed issue - Empty needle issue after 2.0.8 update.

= 2.0.8 (28/10/2015) =
**New Feature**

- New option to exclude paths from error logs and redirect.

**Bug Fixes**

- Fixed issue - Email notifications are being sent even after disabling it.
- Fixed issue - Settings reset after reactivation of plugin.

= 2.0.7 (25/09/2015) =
**New Feature**

- New option to change error notification email address.
- Now **100% Translation ready**.

**Improvements**

- Minor code improvements.

= 2.0.6 (13/09/2015) =
**Improvements**

- Introduced new website for the plugin.
- Fixed few dead link issues

= 2.0.5 (03/09/2015) =
**Improvements**

- Added option to avoid search engine crawlers/bots from logging errors.

**Bug Fixes**

- Fixed error log per page issue.

= 2.0.4 (26/08/2015) =

**Bug Fixes**

- Fixed an issue where error log table is not being created.

= 2.0.3 (21/08/2015) =

**Bug Fixes**

- Fixed a serious issue which may cause SQL injection attack.

= 2.0.2 (16/08/2015) =
**Bug Fixes**

- Fixed an issue with https redirect.
- Fixed an issue with url preg_match.

= 2.0.1 (29/07/2015) =
**New Feature**

- Now you can log/monitor all 404 errors (optional).
- You can get email notifications on 404 errors (optional).
- You can select existing pages from dropdown to set as redirect page.
- New plugin home page.

**Improvements**

- Upgraded to WordPress plugin coding standard.
- Documented all functions.

= 1.0.8 =
* Very minor bug fix
* Tested for WP 4.2

= 1.0.7 =
* Fixed options saving issue in admin page.
* Improved performance.

= 1.0.6 =
* Tested with latest version.
* Improved structure.

= 1.0.5 =
* Bug fix.
* Fixed permission issue on redirect link on plugin activation.

= 1.0.4 =
* Bug fix.
* Fixed permission issue on activating along with some security plugins like WordFence.

= 1.0.3 =
* Added official support forum.

= 1.0.1 =
* Added official website details.

= 1.0.0 =
* Added first version with basic options.

== Upgrade Notice ==

= 3.0.4 (16/03/2019) =
**📦 New**

* Added option to disable URL guessing.
* Added review notice.