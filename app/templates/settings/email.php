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

<h4><?php esc_html_e( 'Enable Email', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'Do you want to receive and email notification for each 404 errors?', '404-to-301' ); ?></p>
<p><?php esc_html_e( 'You will get an email notification for every single 404 error. Please think twice if your site is getting 100s of 404s everyday!', '404-to-301' ); ?></p>
<div class="duckdev-fields">
	<label for="email-enable">
		<input
			type="checkbox"
			name="404_to_301_settings[email][enable]"
			id="email-enable"
			value="1"
			@change="toggleEmail"
			<?php checked( dd4t3_settings()->get( 'enable', 'email' ) ); ?>
		> <?php esc_html_e( 'Enable email notifications for 404 errors?', '404-to-301' ); ?>
	</label>
</div>

<hr/>

<fieldset :class="{'duckdev-disabled': !email}">

	<p>
		<label for="email">
			<strong><?php esc_html_e( 'Recipient email', '404-to-301' ); ?></strong>
		</label>
	</p>
	<p><?php esc_html_e( 'Enter the email address where you want to get the email notification.', '404-to-301' ); ?></p>
	<p>
		<input
			type="email"
			name="404_to_301_settings[email][recipient]"
			id="email"
			class="regular-text"
			placeholder="admin@duckdev.com"
			value="<?php echo esc_html( dd4t3_settings()->get( 'recipient', 'email' ) ); ?>"
		>
	</p>

</fieldset>
