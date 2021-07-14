<?php
/**
 * Admin settings page base template.
 *
 * @var array  $tabs    Tabs list.
 * @var string $current Current tab.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @subpackage Pages
 */

?>

<div class="wrap">

	<h1 class="jj4t3-h2" style="float: left;">404 to 301 <span class="subtitle">by <a href="https://duckdev.com">Joel James</a> ( v3.0.8 )</span></h1>

	<!-- Settings updated message -->

	<h2 class="nav-tab-wrapper">
		<div class="nav-tab-container">
			<a href="?page=jj4t3-settings" class="nav-tab"><span class="dashicons dashicons-admin-generic"></span> General</a>
			<a href="?page=jj4t3-settings" class="nav-tab nav-tab-active"><span class="dashicons dashicons-randomize"></span> Redirect</a>
			<a href="?page=jj4t3-settings" class="nav-tab"><span class="dashicons dashicons-media-default"></span> Logs</a>
			<a href="?page=jj4t3-settings" class="nav-tab"><span class="dashicons dashicons-email-alt"></span> Email</a>
		</div>
	</h2>


	<form method="post" action="options.php">
		<input type="hidden" name="option_page" value="i4t3_gnrl_options"><input type="hidden" name="action" value="update"><input type="hidden" id="_wpnonce" name="_wpnonce" value="e1bfb640cc"><input type="hidden" name="_wp_http_referer" value="/wp-admin/admin.php?page=jj4t3-settings">					<table class="form-table">
			<tbody>
			<tr>
				<th>Redirect type</th>
				<td>
					<select name="i4t3_gnrl_options[redirect_type]">
						<option value="301" selected="selected">301 Redirect (SEO)</option>
						<option value="302">302 Redirect</option>
						<option value="307">307 Redirect</option>
					</select>
					<p class="description jj4t3-p-desc"><a target="_blank" href="https://moz.com/learn/seo/redirection"><strong>Learn more</strong></a> about these redirect types						</p>
				</td>
			</tr>
			<tr>
				<th>Redirect to</th>
				<td>
					<select name="i4t3_gnrl_options[redirect_to]" id="jj4t3_redirect_to">
						<option value="page">Existing Page</option>
						<option value="link" selected="selected">Custom URL</option>
						<option value="0">No Redirect</option>
					</select>
					<p class="description jj4t3-p-desc"><strong>Existing Page:</strong> Select any WordPress page as a 404 page.</p>
					<p class="description jj4t3-p-desc"><strong>Custom URL:</strong> Redirect 404 requests to a specific URL.</p>
					<p class="description jj4t3-p-desc"><strong>No Redirect:</strong> To disable redirect.</p>
					<p class="description jj4t3-p-desc"><strong>You can override this by setting individual custom redirects from error logs list.</strong></p>
				</td>
			</tr>
			<tr id="custom_page" class="jj4t3-hide">
				<th>Select the page</th>
				<td>
					<select name="i4t3_gnrl_options[redirect_page]" id="i4t3_gnrl_options[redirect_page]">
						<option class="level-0" value="92" selected="selected">Cart</option>
						<option class="level-0" value="1042">Checkout</option>
						<option class="level-1" value="1043">&nbsp;&nbsp;&nbsp;Purchase Confirmation</option>
						<option class="level-1" value="1045">&nbsp;&nbsp;&nbsp;Purchase History</option>
						<option class="level-1" value="1044">&nbsp;&nbsp;&nbsp;Transaction Failed</option>
						<option class="level-0" value="93">Checkout</option>
						<option class="level-0" value="117">Contact</option>
						<option class="level-0" value="108">Cookie policy</option>
						<option class="level-0" value="94">My account</option>
						<option class="level-0" value="2">Sample Page</option>
						<option class="level-0" value="91">Shop</option>
					</select>
					<p class="description jj4t3-p-desc">The default 404 page will be replaced by the page you choose in this list.</p>
					<p class="description jj4t3-p-desc">You can <a href="https://insight.wpmudev.host/wp-admin/post-new.php?post_type=page" target="_blank">create a custom 404</a> page and assign that page here.</p>
				</td>
			</tr>
			<tr id="custom_url" class="">
				<th>Custom URL</th>
				<td>
					<input type="url" size="40" placeholder="https://insight.wpmudev.host" name="i4t3_gnrl_options[redirect_link]" value="https://insight.wpmudev.host">
					<p class="description jj4t3-p-desc">Enter any url (including http://)</p>
				</td>
			</tr>
			<tr>
				<th>Log 404 Errors</th>
				<td>
					<input type="checkbox" name="i4t3_gnrl_options[redirect_log]" value="1" checked="checked">
					<p class="description jj4t3-p-desc">Enable/Disable Logging</p>
				</td>
			</tr>
			<tr>
				<th>Email notifications</th>
				<td>
					<input type="checkbox" name="i4t3_gnrl_options[email_notify]" value="1" checked="checked">
					<p class="description jj4t3-p-desc">If you check this, an email will be sent on every 404 log on the admin email account.</p>
				</td>
			</tr>
			<tr>
				<th>Disable URL guessing</th>
				<td>
					<input type="checkbox" name="i4t3_gnrl_options[disable_guessing]" value="1">
					<p class="description jj4t3-p-desc">If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs. <a href="https://developer.wordpress.org/reference/functions/redirect_canonical/" target="_blank">Learn more</a> about canonical redirect.</p>
				</td>
			</tr>
			<tr>
				<th>Email address</th>
				<td>
					<input type="email" placeholder="joel@incsub.com" name="i4t3_gnrl_options[email_notify_address]" value="joel@incsub.com">
					<p class="description jj4t3-p-desc">Change the recipient email address for error log notifications.</p>
				</td>
			</tr>
			<tr>
				<th>Exclude paths</th>
				<td>
					<textarea rows="5" cols="50" placeholder="wp-content/plugins/abc-plugin/css/" name="i4t3_gnrl_options[exclude_paths]">/wp-content</textarea>
					<p class="description jj4t3-p-desc">If you want to exclude few paths from error logs, enter here. One per line.</p>
				</td>
			</tr>
			</tbody>
		</table>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save settings"></p></form><!-- /.form -->
</div>

