<?php
/**
 * Admin settings redirect tab template.
 *
 * @var array                     $types    Redirect types.
 * @var RedirectPress\Settings $settings Settings class.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Pages
 */

?>

<!-- Enable redirects -->
<h4><?php esc_html_e( 'Enable Redirects', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'Do you want to redirect the 404 errors to a new page or URL?', '404-to-301' ); ?></p>
<p>
	<?php
	printf(
	// translators: %s link to logs page.
		__( 'These options can be customized for each individual 404 errors from <a href="%s">the logs page</a>.', '404-to-301' ),
		esc_url( RedirectPress\Plugin::get_url( 'logs' ) )
	);
	?>
</p>
<div class="duckdev-fields">
	<label for="redirect-enabled">
		<input
			type="checkbox"
			id="redirect-enabled"
			name="404_to_301_settings[redirect_enabled]"
			value="1"
			@change="toggleRedirect"
			<?php checked( redirectpress_settings()->get( 'redirect_enabled' ) ); ?>
		> <?php esc_html_e( 'Enable redirects for 404 errors', '404-to-301' ); ?>
	</label>
</div>

<hr>

<!-- Redirect type -->
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

<div class="duckdev-fields">
	<?php $selected = redirectpress_settings()->get( 'redirect_type', 301 ); ?>
	<?php foreach ( $types as $redirect_type => $label ) : ?>
		<p>
			<label for="redirect-type-<?php echo esc_attr( $redirect_type ); ?>">
				<input
					type="radio"
					id="redirect-type-<?php echo esc_attr( $redirect_type ); ?>"
					name="404_to_301_settings[redirect_type]"
					value="<?php echo esc_attr( $redirect_type ); ?>"
					<?php checked( $selected, $redirect_type ); ?>
				> <?php echo esc_attr( $label ); ?>
			</label>
		</p>
	<?php endforeach; ?>
</div>

<hr>

<!-- Redirect target -->
<h4><?php esc_html_e( 'Target', '404-to-301' ); ?></h4>
<p><?php esc_html_e( 'From the target types, choose where you want to redirect the 404 errors to.', '404-to-301' ); ?></p>

<div class="duckdev-fields">
	<p>
		<label for="redirect-target-page">
			<input
				type="radio"
				id="redirect-target-page"
				class="redirect-target"
				name="404_to_301_settings[redirect_target]"
				value="page"
			> <?php esc_html_e( 'Select an existing page on this website', '404-to-301' ); ?>
		</label>
	</p>
	<p>
		<label for="redirect-target-link">
			<input
				type="radio"
				id="redirect-target-link"
				class="redirect-target"
				name="404_to_301_settings[redirect_target]"
				value="link"
			> <?php esc_html_e( 'Enter a custom URL', '404-to-301' ); ?>
		</label>
	</p>
</div>

<!-- Redirect page -->
<div
	id="redirect-target-page-container"
	class="duckdev-fields <?php echo 'page' !== redirectpress_settings()->get( 'redirect_target', 'link' ) ? 'duckdev-hidden' : ''; ?>"
>
	<p>
		<label for="redirect-target-page-value">
			<strong><?php esc_html_e( 'Select page', '404-to-301' ); ?></strong>
		</label>
	</p>
	<p>
		<?php
		wp_dropdown_pages(
			array(
				'id'       => 'redirect-target-page-value',
				'name'     => '404_to_301_settings[redirect_page]',
				'selected' => esc_attr( redirectpress_settings()->get( 'redirect_page' ) ),
			)
		);
		?>
	</p>
</div>

<!-- Redirect link -->
<div
	id="redirect-target-link-container"
	class="duckdev-fields <?php echo 'page' === redirectpress_settings()->get( 'redirect_target', 'link' ) ? 'duckdev-hidden' : ''; ?>"
>
	<p>
		<label for="redirect-target-link-value">
			<strong><?php esc_html_e( 'Custom URL', '404-to-301' ); ?></strong>
		</label>
	</p>
	<p>
		<input
			type="url"
			name="404_to_301_settings[redirect_link]"
			id="redirect-target-link-value"
			class="large-text"
			placeholder="https://example.com"
			value="<?php echo esc_url( redirectpress_settings()->get( 'redirect_link', '' ) ); ?>"
		>
	</p>
</div>
