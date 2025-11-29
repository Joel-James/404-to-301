<?php
/**
 * Admin settings page base template.
 *
 * @var string $page        Current page key.
 * @var string $user_name   Currently logged in user's name.
 * @var array  $menu_config Nav menu configuration.
 *
 * @link       https://duckdev.com/product/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2025, Joel James
 * @package    View
 */

use DuckDev\FourNotFour\Views\View;

?>
<div class="wrap duckdev-wrap" id="404-to-301-settings">
	<h2><strong>🔗 <?php esc_attr_e( '404 to 301 - Settings', '404-to-301' ); ?></strong></h2>
	<?php settings_errors(); ?>
	<br/>
	<?php
	// Render menu.
	View::render(
		'components/nav-menu',
		array(
			'base_url'    => $menu_config['base_url'],
			'tab_items'   => $menu_config['tab_items'],
			'current_tab' => $menu_config['current_tab'],
		)
	);
	?>

	<form action="options.php" method="post">
		<?php settings_fields( duckdev_404_to_301_settings()::KEY ); // Setup form fields. ?>

		<div class="duckdev-notice-wrap">
			<?php
			/**
			 * Action hook to print settings notices.
			 *
			 * @since 4.0.0
			 *
			 * @param string $page Current page.
			 */
			do_action( '404_to_301_admin_notices', 'settings' );

			/**
			 * Action hook to add content to settings form.
			 *
			 * @since 4.0.0
			 */
			do_action( "404_to_301_admin_settings_{$page}_form_content" );

			submit_button( __( 'Save Settings', '404-to-301' ) );
			?>
		</div>
	</form>
</div>
