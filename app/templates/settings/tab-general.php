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

<h2><?php esc_html_e( 'General Settings', '404-to-301' ); ?></h2>
<p><?php esc_html_e( 'Do you want to receive and email notification for each 404 errors?', '404-to-301' ); ?></p>

<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
			<label for="disable-guessing"><?php esc_html_e( 'URL Guessing', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="disable-guessing">
					<input
						type="checkbox"
						name="404_to_301_settings[disable_guessing]"
						id="disable-guessing"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'disable_guessing' ) ); ?>
					> <?php esc_html_e( 'Stop WordPress from guessing URLs', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description"><?php esc_html_e( 'WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.', '404-to-301' ); ?></p>
			<p class="description">
				<?php
				printf(
					__( 'This happens as part of canonical redirect feature. You can learn more about the <a href="%s" target="_blank">canonical redirect</a> in WordPress.', '404-to-301' ),
					'https://developer.wordpress.org/reference/functions/redirect_canonical/'
				);
				?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="monitor-changes"><?php esc_html_e( 'Permalink Changes', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="monitor-changes">
					<input
						type="checkbox"
						name="404_to_301_settings[monitor_changes]"
						id="monitor-changes"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'monitor_changes' ) ); ?>
					> <?php esc_html_e( 'Monitor permalink changes and create redirects', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description">
				<?php esc_html_e( 'New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.', '404-to-301' ); ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="logs-ip-logging"><?php esc_html_e( 'GDPR & Privacy', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="logs-ip-logging">
					<input
						type="checkbox"
						name="404_to_301_settings[ip_logging]"
						id="logs-ip-logging"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'ip_logging' ) ); ?>
					> <?php esc_html_e( 'Do not log visitor\'s IP address', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description">
				<?php esc_html_e( 'To respect visitor\'s privacy and comply with GDPR policies, you may disable a few functionalities of the plugin.', '404-to-301' ); ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="exclude-paths"><?php esc_html_e( 'Exclusions', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="exclude-paths">
					<textarea
						name="404_to_301_settings[exclude_paths]"
						id="exclude-paths"
					>

					</textarea>
				</label>
			</p>
			<p class="description">
				<?php
				printf(
				// translators: %s link to PHP doc for strpos.
					__( 'Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using <code><a href="%s" target="_blank">strpos</a></code> for a match.', '404-to-301' ),
					'https://www.php.net/manual/en/function.strpos.php'
				);
				?>
			</p>
		</td>
	</tr>
	</tbody>
</table>
