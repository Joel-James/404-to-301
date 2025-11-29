<?php
/**
 * Settings page base template.
 *
 * @since      4.0.0
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2025, Joel James
 * @package    View
 */

?>

<h2><?php esc_html_e( 'Log Settings', '404-to-301' ); ?></h2>
<p><?php esc_html_e( 'Do you want to log the 404 errors in detail?', '404-to-301' ); ?></p>

<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
			<label for="logs-enabled"><?php esc_html_e( 'Log Status', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="logs-enabled">
					<input
						type="checkbox"
						name="404_to_301_settings[logs_enabled]"
						id="logs-enabled"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'logs_enabled' ) ); ?>
					>
					<?php esc_html_e( 'Enable logs for 404 errors', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description"><?php esc_html_e( 'Do you want to redirect the 404 errors to a new page or URL?', '404-to-301' ); ?></p>
			<p class="description">
				<?php
				printf(
				// translators: %s link to logs page.
					__( 'These options can be customized for each individual 404 errors from <a href="%s">the logs page</a>.', '404-to-301' ),
					esc_url( DuckDev\FourNotFour\Plugin::get_url( 'logs' ) )
				);
				?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<?php esc_html_e( 'Duplicate Logs', '404-to-301' ); ?>
		</th>
		<td>
			<p>
				<label for="logs-skip-duplicates">
					<input
						type="checkbox"
						name="404_to_301_settings[logs_skip_duplicates]"
						id="logs-skip-duplicates"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'logs_skip_duplicates' ) ); ?>
					> <?php esc_html_e( 'Skip duplicate entries from the logs', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description">
				<?php esc_html_e( 'You may get 100s of visits to an old or non-existing link on your website. This can create 100s of copies of the same 404 link. If you enable this, the duplicates will be skipped without affecting the redirects. This will be helpful to keep your database light.', '404-to-301' ); ?>
			</p>
			<p class="description">
				<?php esc_html_e( 'Please note: Only the 404 url will be checked for duplicates. Visitor details can be different, but still it will be counted as duplicate.', '404-to-301' ); ?>
			</p>
		</td>
	</tr>
	</tbody>
</table>
