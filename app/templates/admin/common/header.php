<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Provide a admin header view for the plugin
 *
 * @link  https://duckdev.com
 * @since 4.0
 *
 * @author Joel James <me@joelsays.com>
 */
?>
<div class="wrap">
	<?php if ( empty( $title ) ) : ?>
		<h2><?php esc_html_e( '404 to 301', '404-to-301' ); ?> <span class="subtitle"><?php printf( __( 'by %1$sJoel James%2$s', '404-to-301' ), '<a href="https://duckdev.com" target="_blank">', '</a>' ); ?> ( v<?php echo DD404_VERSION; ?> )</span></h2>
	<?php endif; ?>
	<?php settings_errors(); // Setting errors. ?>