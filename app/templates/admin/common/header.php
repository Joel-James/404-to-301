<?php

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Provide a admin header view for the plugin
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
?>
    <div class="wrap">
<?php if ( empty( $title ) ) : ?>
    <h2>
        <?php esc_html_e( '404 to 301', '404-to-301' ); ?>
        <span class="subtitle">
            <?php printf( __( 'by <a href="%s" target="_blank">→ Joel James</a>', '404-to-301' ), esc_url( 'https://duckdev.com' ) ); ?> ( v<?php echo DD404_VERSION; ?> )
        </span>
    </h2>
<?php endif; ?>
<?php settings_errors(); // Setting errors. ?>