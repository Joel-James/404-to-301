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

<h4><?php esc_html_e( 'URL Guessing', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.', '404-to-301' ); ?></p>
<p>
	<?php
	printf(
		__( 'This happens as part of canonical redirect feature. You can learn more about the <a href="%s" target="_blank">canonical redirect</a> in WordPress.', '404-to-301' ),
		'https://developer.wordpress.org/reference/functions/redirect_canonical/'
	);
	?>
</p>
<div class="duckdev-fields">
	<label for="guessing">
		<input
			type="checkbox"
			name="404_to_301_settings[general][disable_guess]"
			id="guessing"
			value="1"
			<?php checked( dd4t3_settings()->get( 'disable_guess', 'general' ) ); ?>
		> <?php esc_html_e( 'Stop WordPress from guessing URLs', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<h4><?php esc_html_e( 'Permalink Changes', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.', '404-to-301' ); ?></p>
<div class="duckdev-fields">
	<label for="monitor">
		<input
			type="checkbox"
			name="404_to_301_settings[general][monitor_changes]"
			id="monitor"
			value="1"
			<?php checked( dd4t3_settings()->get( 'monitor_changes', 'general' ) ); ?>
		> <?php esc_html_e( 'Monitor permalink changes and create redirects', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<h4><?php esc_html_e( 'Exclusions', '404-to-301' ); ?></h4>
<p>
	<?php
	printf(
	// translators: %s link to PHP doc for strpos.
		__( 'Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using <code><a href="%s" target="_blank">strpos</a></code> for a match.', '404-to-301' ),
		'https://www.php.net/manual/en/function.strpos.php'
	);
	?>
</p>
<repeat-table></repeat-table>
