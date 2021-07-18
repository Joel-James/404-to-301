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

<table class="form-table" role="presentation">
	<tbody>
	<tr>
		<th scope="row">
			<?php esc_html_e( 'Enable', '404-to-301' ); ?>
			<p class="description font-normal">
				<?php esc_html_e( 'Logs for 404s.', '404-to-301' ); ?>
			</p>
		</th>
		<td>
			<input type="checkbox" name="enable" value="1" v-model="enable">
		</td>
	</tr>
	</tbody>
</table>

<fieldset :disabled="isDisabled">
	<table class="form-table" role="presentation">
		<tbody>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Skip duplicates', '404-to-301' ); ?>
				<p class="description font-normal">
					<?php esc_html_e( 'You can save your db space.', '404-to-301' ); ?>
				</p>
			</th>
			<td>
				<input type="checkbox" name="redirect_log" value="1" checked="checked">
				<span class="sub">
					<?php esc_html_e( 'If you enable this, we will won\'t log the error if the path is already logged.', '404-to-301' ); ?>
				</span>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>