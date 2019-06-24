<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Provide modules admin area view for the plugin
 *
 * @var string $tab Current tab.
 *
 * @author Joel James <me@joelsays.com>
 * @link   https://duckdev.com
 * @since  4.0
 */
?>

<?php DuckDev404\Core\Helpers\General::render_menu( $tab ); // Render admin menu. ?>

<form method="post" action="options.php">

	<?php settings_fields( 'i4t3_gnrl_options' ); // Hidden fields. ?>

	<?php submit_button( __( 'Save Settings', '404-to-301' ) ); ?>
</form>