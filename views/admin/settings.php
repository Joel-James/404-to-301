<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Provide modules admin area view for the plugin
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
?>
<?php DuckDev404\Inc\Helpers\General::render_menu( $tabs, $tab ); // Render admin menu. ?>
<form method="post" action="options.php"></form>