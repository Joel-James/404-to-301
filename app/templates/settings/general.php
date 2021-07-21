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
	<span class="tsf-toblock">
		<label for="guessing">
			<input type="checkbox" name="guessing" id="guessing" value="1" checked="checked"> Disable guessing?
		</label>
	</span>
</div>

<hr/>

<h4><?php esc_html_e( 'Permalink Changes', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<div class="tsf-fields">
	<span class="tsf-toblock">
		<label for="monitor">
			<input type="checkbox" name="monitor" id="monitor" value="1" checked="checked"> <?php esc_html_e( 'Monitor Permalink Changes', '404-to-301' ); ?>
		</label>
	</span>
</div>

<hr/>

<h4><?php esc_html_e( 'Exclusions', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?></p>
<table class="tsf-table w-full">
	<tbody>
	<tr>
		<td><input type="text" class="large-text" value="/wp-content" placeholder="wp-content/"></td>
		<td>
			<button
				type="button"
				class="button inline-flex items-center"
			>
				<span class="dashicons dashicons-trash"></span>
			</button>
		</td>
	</tr>
	<tr>
		<td><input type="text" class="large-text" value="/wp-plugins" placeholder="wp-content/"></td>
		<td>
			<button
				type="button"
				class="button inline-flex items-center"
			>
				<span class="dashicons dashicons-trash"></span>
			</button>
		</td>
	</tr>
	</tbody>
	<tfoot>
	<tr>
		<td colspan="2">
			<button
				type="button"
				class="tsf-set-image-button button button-primarys button-small inline-flex items-center"
			>
				<span class="dashicons dashicons-plus"></span>
				<?php esc_html_e( 'Add', '404-to-301' ); ?>
			</button>
		</td>
	</tr>
	</tfoot>
</table>
