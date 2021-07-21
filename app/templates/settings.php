<?php
/**
 * Admin settings page base template.
 *
 * @var string $page        Current page key.
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
<div class="wrap tsf-metaboxes" id="dd404-settings-app">
	<form method="post" action="options.php" autocomplete="off">

		<div class="tsf-top-wrap">
			<h1 class="font-bold mb-5">
				<?php esc_html_e( '404 to 301', '404-to-301' ); ?>
				<span class="subtitle pl-4">
					by <a href="https://duckdev.com/?utm_source=dd404&utm_medium=plugin&utm_campaign=dd404_settings_header">Joel James</a> ( v<?php echo esc_attr( DD4T3_VERSION ); ?> )
				</span>
			</h1>
			<p class="tsf-top-buttons">
				<input type="submit" name="submit" class="button button-primary" value="Save Settings">
				<input type="submit" name="reset" class="button" value="<?php esc_html_e( 'Reset Settings', '404-to-301' ); ?>" onclick="return confirm(`<?php esc_html_e( 'Are you sure you want to reset all settings to their defaults?', '404-to-301' ); ?>`)">
			</p>
		</div>

		<hr class="wp-header-end">

		<div class="tsf-notice-wrap">
			<div class="notice updated tsf-notice tsf-show-icon is-dismissible">
				<p><?php esc_html_e( 'Settings updated!', '404-to-301' ); ?></p>
			</div>
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
							<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

							<div class="tsf-tabs-content tsf-general-tabs-content tsf-active-tab-content" id="tsf-general-tab-<?php echo esc_attr( $page ); ?>-content">
								<?php
								/**
								 * Action hook to add content to settings form.
								 *
								 * @since 4.0.0
								 */
								do_action( "dd404_admin_settings_{$page}_form_content" );
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="postbox-container-2"></div>
		</div>

		<div class="tsf-bottom-buttons">
			<input type="submit" name="submit" class="button button-primary" value="Save Settings">
			<input type="submit" name="reset" class="button" value="<?php esc_html_e( 'Reset Settings', '404-to-301' ); ?>" onclick="return confirm(`<?php esc_html_e( 'Are you sure you want to reset all settings to their defaults?', '404-to-301' ); ?>`)">
		</div>
	</form>
</div>
