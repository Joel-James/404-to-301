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
			<?php esc_html_e( 'Disable URL Guessing', '404-to-301' ); ?>
		</th>
		<td>
			<input type="checkbox" name="i4t3_gnrl_options[redirect_log]" value="1">
			<span class="sub">
				<?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?>
			</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Monitor Permalink Changes', '404-to-301' ); ?></th>
		<td>
			<input type="checkbox" name="i4t3_gnrl_options[redirect_log]" value="1" checked="checked">
			<span class="sub">
				<?php esc_html_e( 'If you disable URL guessing, it will stop WordPress from autocorrecting incorrect URLs.', '404-to-301' ); ?>
			</span>
		</td>
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Exclude Paths', '404-to-301' ); ?></th>
		<td>
			<table>
				<tbody>
				<tr>
					<td><input type="text" value="/wp-content"></td>
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
					<td><input type="text" value="/wp-plugins"></td>
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
							class="button inline-flex items-center"
						>
							<span class="dashicons dashicons-plus"></span>
							<?php esc_html_e( 'Add New', '404-to-301' ); ?>
						</button>
					</td>
				</tr>
				</tfoot>
			</table>
		</td>
	</tr>
	</tbody>
</table>
