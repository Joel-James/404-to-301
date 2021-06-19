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

<main class="max-w-7xl pb-10 lg:py-12 lg:px-8" id="dd404-settings-app">
	<div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
		<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

		<!-- Payment details -->
		<div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
			<section aria-labelledby="dd404-settings-<?php echo esc_attr( $page ); ?>-form-title">
				<form action="#" method="POST">
					<div class="shadow sm:rounded-md sm:overflow-hidden">
						<div class="bg-white py-6 px-4 sm:p-6">
							<input type="hidden" v-model="nonce" name="nonce" value="<?php echo esc_html( wp_create_nonce( 'dd404-settings-form' ) ); ?>">
							<input type="hidden" v-model="page" name="page" value="<?php echo esc_attr( $page ); ?>">
							<?php
							/**
							 * Action hook to add content to settings form.
							 *
							 * @since 4.0.0
							 */
							do_action( "dd404_admin_settings_{$page}_form_content" );
							?>
						</div>
						<form-submit-button
							save-text="<?php esc_html_e( 'Save', '404-to-301' ); ?>"
							saving-text="<?php esc_html_e( 'Saving', '404-to-301' ); ?>"
						></form-submit-button>
					</div>
				</form>
			</section>
		</div>
	</div>
</main>

