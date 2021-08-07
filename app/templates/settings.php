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
	<form method="post" action="options.php" autocomplete="off">

		<?php settings_fields( dd4t3_settings()::KEY ); // Setup form fields. ?>

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

		<hr class="wp-header-end">

		<div class="duckdev-notice-wrap">
			<?php
			/**
			 * Action hook to print settings notices.
			 *
			 * @param string $page Current page.
			 *
			 * @since 4.0.0
			 */
			do_action( 'dd4t3_admin_notices', 'settings' );
			?>
		</div>

		<div class="metabox-holder columns-2">
			<div class="postbox-container-1">
				<div id="main-sortables" class="meta-box-sortables ui-sortable">
					<div id="autodescription-general-settings" class="postbox ">
						<div class="postbox-header">
							<h2 class="hndle ui-sortable-handle">
								<?php esc_html_e( 'Settings', '404-to-301' ); ?>
							</h2>
						</div>

						<div class="inside">
							<p><?php printf( esc_html__( 'Hey %s. You can configure how 404 to 301 plugin should handle 404 errors on your website.', '404-to-301' ), $user_name ); // phpcs:ignore ?></p>
							<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

							<div class="duckdev-tabs-content duckdev-active-tab-content" id="duckdev-tab-<?php echo esc_attr( $page ); ?>-content">
								<?php
								/**
								 * Action hook to add content to settings form.
								 *
								 * @since 4.0.0
								 */
								do_action( "dd4t3_admin_settings_{$page}_form_content" );
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="postbox-container-2"></div>
		</div>

		<?php if ( $this->show_submit() ) : ?>
			<div class="duckdev-bottom-buttons">
				<form-submit
					save-text="<?php esc_html_e( 'Save Settings', '404-to-301' ); ?>"
					saving-text="<?php esc_html_e( 'Saving..', '404-to-301' ); ?>"
					reset-text="<?php esc_html_e( 'Reset Settings', '404-to-301' ); ?>"
					resetting-text="<?php esc_html_e( 'Resetting..', '404-to-301' ); ?>"
				></form-submit>
			</div>
		<?php endif; ?>
	</form>
</div>
