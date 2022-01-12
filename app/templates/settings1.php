<?php
/**
 * Admin settings page base template.
 *
 * @var string $page        Current page key.
 * @var string $user_name   Currently logged in user's name.
 * @var array  $menu_config Nav menu configuration.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @since      4.0
 *
 * @subpackage Pages
 */

?>
<div class="wrap duckdev-wrap" id="dd4t3-settings-app">
	<div class="duckdev-top-wrap">
		<h1>
			<?php esc_html_e( '404 to 301', '404-to-301' ); ?>
			<span class="subtitle">
					by <a href="https://duckdev.com/?utm_source=dd4t3&utm_medium=plugin&utm_campaign=dd4t3_settings_header">Joel James</a> ( v<?php echo esc_attr( DD4T3_VERSION ); ?> )
				</span>
		</h1>

		<?php if ( $this->show_submit() ) : ?>
			<p class="duckdev-top-buttons">
				<form-submit
					save-text="<?php esc_html_e( 'Save Settings', '404-to-301' ); ?>"
					saving-text="<?php esc_html_e( 'Saving..', '404-to-301' ); ?>"
					reset-text="<?php esc_html_e( 'Reset Settings', '404-to-301' ); ?>"
					resetting-text="<?php esc_html_e( 'Resetting..', '404-to-301' ); ?>"
				></form-submit>
			</p>
		<?php endif; ?>
	</div>

	<h2 class="nav-tab-wrapper">
		<a href="javascript:void(0)" class="nav-tab nav-tab-active">
			Redirect
		</a>
		<a href="javascript:void(0)" class="nav-tab">
			Logs
		</a>
		<a href="javascript:void(0)" class="nav-tab">
			Email
		</a>
		<a href="javascript:void(0)" class="nav-tab">
			<span class="dashicons dashicons-admin-generic duckdev-dashicons-tabs"></span>
			General
		</a>
		<a href="javascript:void(0)" class="nav-tab">
			<span class="dashicons dashicons-info duckdev-dashicons-tabs"></span>
			Info
		</a>
	</h2>
	<div class="tabs-content">
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">Logs</th>
				<td>
					<fieldset>
						<legend class="screen-reader-text">
							<span>checkbox</span>
						</legend>
						<label for="checkbox_id">
							<input name="checkbox_id" type="checkbox" id="checkbox_id" value="1">
							<?php esc_html_e( 'Enable redirects for 404 errors', '404-to-301' ); ?>
						</label>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="select_id"><?php esc_html_e( 'Redirect type', '404-to-301' ); ?></label></th>
				<td>
					<?php foreach ( $types as $redirect_type => $label ) : ?>
						<p>
							<label for="redirect-type-<?php echo esc_attr( $redirect_type ); ?>">
								<input
									type="radio"
									id="redirect-type-<?php echo esc_attr( $redirect_type ); ?>"
									name="404_to_301_settings[redirect_type]"
									value="<?php echo esc_attr( $redirect_type ); ?>"
									<?php checked( dd4t3_settings()->get( 'redirect_type' ), $redirect_type ); ?>
								> <?php echo esc_attr( $label ); ?>
							</label>
						</p>
					<?php endforeach; ?>
					<p class="description">
						<?php esc_html_e( 'The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.', '404-to-301' ); ?>
						<?php
						printf(
							__( 'Learn more about HTTP redirect types on <a href="%s" target="_blank">MDN Web Docs</a> before you decide the type.', '404-to-301' ),
							'https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections'
						);
						?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="select_id"><?php esc_html_e( 'Target', '404-to-301' ); ?></label></th>
				<td>
					<p>
						<label for="redirect-target-page">
							<input
								type="radio"
								id="redirect-target-page"
								name="404_to_301_settings[redirect_target]"
								value="page"
								v-model="target"
							> <?php esc_html_e( 'Select an existing page', '404-to-301' ); ?>
						</label>
					</p>
					<p>
						<label for="redirect-target-link">
							<input
								type="radio"
								id="redirect-target-link"
								name="404_to_301_settings[redirect_target]"
								value="link"
								v-model="target"
							> <?php esc_html_e( 'Enter a custom URL', '404-to-301' ); ?>
						</label>
					</p>
					<p class="description">
						<?php esc_html_e( 'From the target types, choose where you want to redirect the 404 errors to.', '404-to-301' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="select_id"><?php esc_html_e( 'Select page', '404-to-301' ); ?></label>
				</th>
				<td>
					<p>
						<?php
						wp_dropdown_pages(
							array(
								'id'       => 'redirect-target-page-value',
								'name'     => '404_to_301_settings[redirect_page]',
								'selected' => esc_attr( dd4t3_settings()->get( 'redirect_page' ) ),
							)
						);
						?>
					</p>
				</td>
			</tr>

			<tr>
				<th scope="row">
					<label for="select_id"><?php esc_html_e( 'Custom URL', '404-to-301' ); ?></label>
				</th>
				<td>
					<p>
						<input
							type="url"
							name="404_to_301_settings[redirect_link]"
							id="redirect-target-link-value"
							class="large-text"
							placeholder="https://example.com"
							value="<?php echo esc_url( dd4t3_settings()->get( 'redirect_link', '' ) ); ?>"
						>
					</p>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
