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
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
	<label for="guessing">
		<input
			type="checkbox"
			name="404_to_301_settings[general][disable_guess]"
			id="guessing"
			value="1"
			<?php checked( dd404_settings()->get( 'disable_guess', 'general' ) ); ?>
		> <?php esc_html_e( 'Disable guessing?', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<h4><?php esc_html_e( 'Permalink Changes', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
	<label for="monitor">
		<input
			type="checkbox"
			name="404_to_301_settings[general][monitor_changes]"
			id="monitor"
			value="1"
			<?php checked( dd404_settings()->get( 'monitor_changes', 'general' ) ); ?>
		> <?php esc_html_e( 'Monitor Permalink Changes', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<h4><?php esc_html_e( 'Exclusions', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<repeat-table></repeat-table>
