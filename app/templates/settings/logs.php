<?php
/**
 * Admin settings general tab template.
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

<h4><?php esc_html_e( 'Enable Logs', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'Do you want to log the 404 errors in detail?', '404-to-301' ); ?></p>
<p><?php esc_html_e( 'This will be helpful for you to keep track of broken links to your website. You can also setup individual redirects for each 404s from the logs page.', '404-to-301' ); ?></p>
<div class="duckdev-fields">
	<label for="logs-enable">
		<input
			type="checkbox"
			name="404_to_301_settings[logs][enable]"
			id="logs-enable"
			value="1"
			@change="toggleLogs"
			<?php checked( dd404_settings()->get( 'enable', 'logs' ) ); ?>
		> <?php esc_html_e( 'Enable logs for 404 errors', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<fieldset :class="{'duckdev-disabled': !logs}">

	<h4><?php esc_html_e( 'Handling Duplicate Logs', '404-to-301' ); ?></h4>
	<p><?php esc_html_e( 'You may get 100s of visits to an old or non-existing link on your website. This can create 100s of copies of the same 404 link. If you enable this, the duplicates will be skipped without affecting the redirects. This will be helpful to keep your database light.', '404-to-301' ); ?></p>
	<p><?php esc_html_e( 'Please note: Only the 404 url will be checked for duplicates. Visitor details can be different, but still it will be counted as duplicate.', '404-to-301' ); ?></p>
	<div class="duckdev-fields">
		<label for="duplicates">
			<input
				type="checkbox"
				name="404_to_301_settings[logs][skip_duplicates]"
				id="duplicates"
				value="1"
				<?php checked( dd404_settings()->get( 'skip_duplicates', 'logs' ) ); ?>
			> <?php esc_html_e( 'Skip duplicate entries from the logs', '404-to-301' ); ?>
		</label>
	</div>

</fieldset>
