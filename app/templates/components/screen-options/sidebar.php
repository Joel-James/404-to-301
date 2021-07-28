<?php
/**
 * Screen options sidebar template.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage ScreenOptions
 */

?>

<p><strong><?php esc_html_e( 'Information', '404-to-301' ); ?></strong></p>
<p>
	<span class="dashicons dashicons-admin-plugins"></span>
	<?php printf( esc_html__( 'Version %s', '404-to-301' ), DD404_VERSION ); // phpcs:ignore ?>
</p>
<p>
	<span class="dashicons dashicons-wordpress"></span>
	<a href="https://wordpress.org/plugins/404-to-301/" target="_blank">
		<?php esc_html_e( 'View details', '404-to-301' ); ?>
	</a>
</p>
<p>
	<span class="dashicons dashicons-admin-home"></span>
	<a href="https://duckdev.com/products/404-to-301/" target="_blank">
		<?php esc_html_e( 'Visit website', '404-to-301' ); ?>
	</a>
</p>
