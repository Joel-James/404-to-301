<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) or exit;

/**
 * Provide a dashboard view for the plugin.
 *
 * This file is used to markup the dashboard pages of the plugin.
 *
 * @category   Core
 * @package    I4T3
 * @subpackage Admin View
 * @author     Joel James <mail@cjoel.com>
 * @license    http://www.gnu.org/licenses/ GNU General Public License
 * @link       https://duckdev.com/products/404-to-301
 */
?>
<div class="wrap">

	<h2 class="jj4t3-h2"><?php _e( '404 to 301', '404-to-301' ); ?> <span class="subtitle"><?php printf( __( 'by <a href="%s">Joel James</a>', '404-to-301' ), 'https://duckdev.com' ); ?> ( v<?php echo JJ4T3_VERSION; ?> )</span></h2><br/>

	<!-- Settings updated message -->
	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=jj4t3-settings" class="nav-tab nav-tab-active"><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'Settings', '404-to-301' ); ?></a>
		<?php do_action( 'jj4t3_settings_tab' ); // Action hook to add new items to tab. ?>
	</h2>

	<?php require_once 'settings.php'; ?>

</div><!-- /.wrap -->