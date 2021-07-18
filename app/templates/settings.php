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
<div class="wrap max-w-screen-xl" id="dd404-settings-app">
	<h1 class="font-bold mb-5">
		<?php esc_html_e( '404 to 301', '404-to-301' ); ?>
		<span class="subtitle pl-4">
			by <a href="https://duckdev.com/?utm_source=dd404&utm_medium=plugin&utm_campaign=dd404_settings_header">Joel James</a> ( v<?php echo esc_attr( DD4T3_VERSION ); ?> )
		</span>
	</h1>

	<div class="notice notice-success is-dismissible">
		<p><?php esc_html_e( 'Settings updated!', '404-to-301' ); ?></p>
	</div>

	<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

	<form method="post" action="">
		<input
			type="hidden"
			v-model="nonce"
			name="nonce"
			value="<?php echo esc_html( wp_create_nonce( 'dd404-settings-form' ) ); ?>"
		>
		<input
			type="hidden"
			v-model="page"
			name="page"
			value="<?php echo esc_attr( $page ); ?>"
		>

		<?php
		/**
		 * Action hook to add content to settings form.
		 *
		 * @since 4.0.0
		 */
		do_action( "dd404_admin_settings_{$page}_form_content" );
		?>

		<p class="submit">
			<button
				type="submit"
				class="button button-primary inline-flex items-center py-0.5"
			>
				<span class="dashicons dashicons-yes"></span>
				<?php esc_html_e( 'Save Changes', '404-to-301' ); ?>
			</button>
		</p>
	</form>
</div>
