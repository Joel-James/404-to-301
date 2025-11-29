<?php
/**
 * Settings page base template.
 *
 * @since      4.0.0
 *
 * @var array $types Redirect types.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2025, Joel James
 * @package    View
 */

?>

<h2><?php esc_html_e( 'Redirect Settings', '404-to-301' ); ?></h2>
<p><?php esc_html_e( 'Do you want to redirect the 404 errors to a new page or URL?', '404-to-301' ); ?></p>

<table class="form-table">
	<tbody>
	<tr>
		<th scope="row">
			<label for="redirect-enabled"><?php esc_html_e( 'Redirect Status', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="redirect-enabled">
					<input
						type="checkbox"
						id="redirect-enabled"
						name="404_to_301_settings[redirect_enabled]"
						value="1"
						<?php checked( duckdev_404_to_301_settings()->get( 'redirect_enabled' ) ); ?>
					>
					<?php esc_html_e( 'Enable redirects for 404 errors', '404-to-301' ); ?>
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
			<?php esc_attr_e( 'Redirect Type', '404-to-301' ); ?>
		</th>
		<td>
			<?php $selected = duckdev_404_to_301_settings()->get( 'redirect_type', 301 ); ?>
			<?php foreach ( $types as $redirect_type => $label ) : ?>
				<p>
					<label for="redirect-type-<?php echo esc_attr( $redirect_type ); ?>">
						<input
							type="radio"
							id="redirect-type-<?php echo esc_attr( $redirect_type ); ?>"
							name="404_to_301_settings[redirect_type]"
							value="<?php echo esc_attr( $redirect_type ); ?>"
							<?php checked( $selected, $redirect_type ); ?>
						>
						<?php echo esc_attr( $label ); ?>
					</label>
				</p>
			<?php endforeach; ?>
			<p class="description">
				<?php esc_html_e( 'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.', '404-to-301' ); ?>
			</p>
			<p class="description">
				<?php
				printf(
					__( 'Learn more about HTTP redirect types on <a href="%s" target="_blank">MDN Web Docs</a> before you select decide the type.', '404-to-301' ),
					'https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections'
				);
				?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="redirect-target-page"><?php esc_attr_e( 'Redirect Target', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<label for="redirect-target-page">
					<input
						type="radio"
						id="redirect-target-page"
						name="404_to_301_settings[redirect_target]"
						value="page"
					>
					<?php esc_html_e( 'Select an existing page on this website', '404-to-301' ); ?>
				</label>
			</p>
			<p>
				<label for="redirect-target-link">
					<input
						type="radio"
						id="redirect-target-link"
						name="404_to_301_settings[redirect_target]"
						value="link"
					>
					<?php esc_html_e( 'Enter a custom URL', '404-to-301' ); ?>
				</label>
			</p>
			<p class="description"><?php esc_html_e( 'From the target types, choose where you want to redirect the 404 errors to.', '404-to-301' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="redirect-target-page-value"><?php esc_html_e( 'Select page', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<?php
				wp_dropdown_pages(
					array(
						'id'       => 'redirect-target-page-value',
						'name'     => '404_to_301_settings[redirect_page]',
						'selected' => esc_attr( duckdev_404_to_301_settings()->get( 'redirect_page' ) ),
					)
				);
				?>
			</p>
			<p class="description"><?php esc_html_e( 'Select a target page for redirect.', '404-to-301' ); ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row">
			<label for="redirect-target-link-value"><?php esc_html_e( 'Custom URL', '404-to-301' ); ?></label>
		</th>
		<td>
			<p>
				<input
					type="url"
					name="404_to_301_settings[redirect_link]"
					id="redirect-target-link-value"
					class="regular-text"
					placeholder="https://example.com"
					value="<?php echo esc_url( duckdev_404_to_301_settings()->get( 'redirect_link', '' ) ); ?>"
				>
			</p>
			<p class="description"><?php esc_html_e( 'Select a target page for redirect.', '404-to-301' ); ?></p>
		</td>
	</tr>
	</tbody>
</table>
