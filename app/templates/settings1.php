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
<div class="wrap duckdev-wrap" id="redirectpress-settings-app">
	<form method="post" action="options.php" autocomplete="off">

		<?php settings_fields( redirectpress_settings()::KEY ); // Setup form fields. ?>

		<div class="duckdev-top-wrap">
			<h1>
				<?php esc_html_e( '404 to 301', '404-to-301' ); ?>
				<span class="subtitle">
					by <a href="https://duckdev.com/?utm_source=redirectpress&utm_medium=plugin&utm_campaign=redirectpress_settings_header">Joel James</a> ( v<?php echo esc_attr( REDIRECT_VERSION ); ?> )
				</span>
			</h1>
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
			do_action( 'redirectpress_admin_notices', 'settings' );
			?>
		</div>

		<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

		<div class="metabox-holder columns-2">
			<div class="postbox-container-1">
				<div id="main-sortables" class="meta-box-sortables ui-sortable">
					<div class="postbox ">

						<div class="inside">
							<div class="duckdev-tabs-content duckdev-active-tab-content" id="duckdev-tab-<?php echo esc_attr( $page ); ?>-content">
								<?php
								/**
								 * Action hook to add content to settings form.
								 *
								 * @since 4.0.0
								 */
								do_action( "redirectpress_admin_settings_{$page}_form_content" );
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="postbox-container-2"></div>
		</div>

		<button
			type="submit"
			class="button button-primary"
			id="redirectpress-settings-submit"
		>
			<?php esc_html_e( 'Save Changes', '404-to-301' ); ?>
		</button>
	</form>
</div>
