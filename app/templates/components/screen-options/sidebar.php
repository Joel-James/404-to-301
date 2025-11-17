<?php
/**
 * Screen options sidebar template.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @package    View
 * @subpackage ScreenOptions
 */

?>

<p><strong><?php esc_html_e( 'Information', '404-to-301' ); ?></strong></p>
<p>
	<span class="dashicons dashicons-admin-plugins"></span>
	<?php
	printf(
		// translators: %s plugin version.
		esc_html__( 'Version %s', '404-to-301' ),
		esc_attr( DUCKDEV_404_VERSION )
	);
	?>
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
