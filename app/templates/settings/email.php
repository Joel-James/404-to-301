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

<h4><?php esc_html_e( 'Enable', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
		<label for="enable">
			<input
				type="checkbox"
				name="404_to_301_settings[email][enable]"
				id="enable"
				value="1"
				v-model="email"
			> <?php esc_html_e( 'Enable email notification?', '404-to-301' ); ?>
		</label>
</div>

<hr/>

<fieldset :disabled="!email">
	<p>
		<label for="email">
			<strong><?php esc_html_e( 'Recipient email', '404-to-301' ); ?></strong>
		</label>
	</p>
	<p>
		<input
			type="email"
			name="404_to_301_settings[email][recipient]"
			id="email"
			class="regular-text"
			placeholder="admin@duckdev.com"
			value="<?php echo esc_html( dd404_settings()->get( 'recipient', 'email' ) ); ?>"
		>
	</p>
	<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
</fieldset>
