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

<h2><?php esc_html_e( 'Notification Settings', '404-to-301' ); ?></h2>
<p><?php esc_html_e( 'Do you want to receive and email notification for each 404 errors?', '404-to-301' ); ?></p>

<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
			<label for="email-enabled"><?php esc_html_e( 'Notification Status', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="email-enabled">
					<input
						type="checkbox"
						name="404_to_301_settings[email_enable]"
						id="email-enabled"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'email_enabled' ) ); ?>
					>
					<?php esc_html_e( 'Enable email notifications for 404 errors?', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description"><?php esc_html_e( 'You will get an email notification for every single 404 error. Please think twice if your site is getting 100s of 404s everyday!', '404-to-301' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="email-recipient"><?php esc_html_e( 'Recipient email', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<input
					type="email"
					name="404_to_301_settings[email_recipient]"
					id="email-recipient"
					class="regular-text"
					placeholder="admin@duckdev.com"
					value="<?php echo esc_html( duckdev_404_to_301_settings()->get( 'email_recipient', '' ) ); ?>"
				>
			</p>
			<p class="description">
				<?php esc_html_e( 'Enter the email address where you want to get the email notification.', '404-to-301' ); ?>
			</p>
		</td>
	</tr>
	</tbody>
</table>
