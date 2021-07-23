<?php
/**
 * Admin settings redirect tab template.
 *
 * @var DuckDev\Redirect\Controllers\Settings $settings Settings class.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

?>

<h4><?php esc_html_e( 'Enable Redirects', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'Do you want to redirect the 404 errors to a new page or URL?', '404-to-301' ); ?></p>
<p>
	<?php
	printf(
		// translators: %s link to logs page.
		__( 'These options can be customized for each individual 404 errors from <a href="%s">the logs page</a>.', '404-to-301' ),
		'' // @todo Add link.
	);
	?>
</p>
<div class="tsf-fields">
	<label for="enable">
		<input
			type="checkbox"
			name="404_to_301_settings[redirect][enable]"
			value="1"
			id="enable"
			v-model="redirect"
		> <?php esc_html_e( 'Enable redirects for 404 errors', '404-to-301' ); ?>
	</label>
</div>

<hr>

<fieldset :disabled="!redirect">
	<h4><?php esc_html_e( 'Redirect type', '404-to-301' ); ?></h4>
	<p><?php esc_html_e( 'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.', '404-to-301' ); ?></p>
	<p>
		<?php
		printf(
			__( 'Learn more about HTTP redirect types on <a href="%s" target="_blank">MDN Web Docs</a> before you select decide the type.', '404-to-301' ),
			'https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections'
		);
		?>
	</p>

	<div class="tsf-fields">
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][type]"
					value="301"
					<?php checked( dd404_settings()->get( 'type', 'redirect', 301 ), 301 ); ?>
				> <?php esc_html_e( '301 - Moved Permanently', '404-to-301' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][type]"
					value="302"
					<?php checked( dd404_settings()->get( 'type', 'redirect', 301 ), 302 ); ?>
				> <?php esc_html_e( '302 - Found', '404-to-301' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][type]"
					value="307"
					<?php checked( dd404_settings()->get( 'type', 'redirect', 301 ), 307 ); ?>
				> <?php esc_html_e( '307 - Temporary Redirect', '404-to-301' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][type]"
					value="410"
					<?php checked( dd404_settings()->get( 'type', 'redirect', 301 ), 410 ); ?>
				> <?php esc_html_e( '410 - Content Deleted', '404-to-301' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][type]"
					value="451"
					<?php checked( dd404_settings()->get( 'type', 'redirect', 301 ), 451 ); ?>
				> <?php esc_html_e( '451 - Unavailable for Legal Reasons', '404-to-301' ); ?>
			</label>
		</p>
	</div>

	<hr>

	<h4><?php esc_html_e( 'Target', '404-to-301' ); ?></h4>
	<p><?php esc_html_e( 'From the target types, choose where you want to redirect the 404 errors to.', '404-to-301' ); ?></p>

	<div class="tsf-fields">
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][target]"
					value="page"
					v-model="target"
				> <?php esc_html_e( 'Select an existing page on this website', '404-to-301' ); ?>
			</label>
		</p>
		<p>
			<label>
				<input
					type="radio"
					name="404_to_301_settings[redirect][target]"
					value="link"
					v-model="target"
				> <?php esc_html_e( 'Enter a custom URL', '404-to-301' ); ?>
			</label>
		</p>
	</div>


	<div class="tsf-fields" v-if="'page' === target">
		<p>
			<label for="page">
				<strong><?php esc_html_e( 'Select page', '404-to-301' ); ?></strong>
			</label>
		</p>
		<p>
			<?php
			wp_dropdown_pages(
				array(
					'name'     => '404_to_301_settings[redirect][page]',
					'selected' => dd404_settings()->get( 'page', 'redirect' ),
				)
			);
			?>
		</p>
	</div>

	<div class="tsf-fields" v-else-if="'link' === target">
		<p>
			<label for="url">
				<strong><?php esc_html_e( 'Custom URL', '404-to-301' ); ?></strong>
			</label>
		</p>
		<p>
			<input
				type="url"
				name="404_to_301_settings[redirect][link]"
				id="url"
				class="large-text"
				placeholder="https://example.com"
				value="<?php echo esc_url( dd404_settings()->get( 'link', 'redirect', '' ) ); ?>"
			>
		</p>
	</div>

</fieldset>
