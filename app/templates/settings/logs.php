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
			name="404_to_301_settings[logs][enable]"
			id="enable"
			value="1"
			v-model="logs"
		> <?php esc_html_e( 'Enable logs?', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<fieldset :disabled="!logs">

	<h4><?php esc_html_e( 'Duplicates', '404-to-301' ); ?></h4>
	<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
	<div class="tsf-fields">
		<label for="duplicates">
			<input
				type="checkbox"
				name="404_to_301_settings[logs][skip_duplicates]"
				id="duplicates"
				value="1"
				<?php checked( dd404_settings()->get( 'skip_duplicates', 'logs' ) ); ?>
			> <?php esc_html_e( 'Skip duplicates?', '404-to-301' ); ?>
		</label>
	</div>

</fieldset>
