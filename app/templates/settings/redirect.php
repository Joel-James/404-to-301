<?php
/**
 * Admin settings redirect tab template.
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
				<?php esc_html_e( 'Redirect for 404s.', '404-to-301' ); ?>
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
				<?php esc_html_e( 'Type', '404-to-301' ); ?>
				<p class="description font-normal">
					<?php esc_html_e( 'Type of redirect status.', '404-to-301' ); ?>
				</p>
			</th>
			<td>
				<div class="grid grid-cols-1">
					<div class="py-2"><input type="radio" name="target" value="1"> <?php esc_html_e( '301 Redirect', '404-to-301' ); ?></div>
					<div class="py-2"><input type="radio" name="target" value="2"> <?php esc_html_e( '302 Redirect', '404-to-301' ); ?></div>
					<div class="py-2"><input type="radio" name="target" value="3"> <?php esc_html_e( '307 Redirect', '404-to-301' ); ?></div>
				</div>
			</td>
		</tr>
		<tr>
			<th>
				<?php esc_html_e( 'Target', '404-to-301' ); ?>
				<p class="description font-normal">
					<?php esc_html_e( 'Where to redirect?', '404-to-301' ); ?>
				</p>
			</th>
			<td>
				<div class="grid grid-cols-1">
					<div class="py-2"><input type="radio" name="target" value="1"> <?php esc_html_e( 'Select page', '404-to-301' ); ?></div>
					<div class="py-2"><input type="radio" name="target" value="2"> <?php esc_html_e( 'Custom URL', '404-to-301' ); ?></div>
				</div>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Custom URL', '404-to-301' ); ?>
				<p class="description font-normal">
					<?php esc_html_e( 'Full URL to redirect to.', '404-to-301' ); ?>
				</p>
			</th>
			<td>
				<input type="url">
			</td>
		</tr>
		<tr>
			<th scope="row">
				<?php esc_html_e( 'Select Page', '404-to-301' ); ?>
				<p class="description font-normal">
					<?php esc_html_e( 'Existing page to redirect to.', '404-to-301' ); ?>
				</p>
			</th>
			<td>
				<?php
				wp_dropdown_pages(
					array(
						'name'     => 'page',
						'selected' => 'home',
					)
				);
				?>
			</td>
		</tr>
		</tbody>
	</table>
</fieldset>
