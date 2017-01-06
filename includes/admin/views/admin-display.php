<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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

	<h2 class="jj4t3-h2"><?php _e( '404 to 301', JJ4T3_DOMAIN ); ?> <span class="subtitle"><?php printf( __( 'by <a href="%s">Joel James</a>', JJ4T3_DOMAIN ), 'https://duckdev.com' ); ?> ( v<?php echo JJ4T3_VERSION; ?> )</span></h2><br/>

	<?php $tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general'; ?>

	<!-- Settings updated message -->
	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=jj4t3-settings" class="nav-tab <?php echo $tab === 'general' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', JJ4T3_DOMAIN ); ?></a>
		<a href="?page=jj4t3-settings&tab=help" class="nav-tab <?php echo $tab === 'help' ? 'nav-tab-active' : ''; ?>"><span class="dashicons dashicons-editor-help"></span> <?php _e( 'Help & Info', JJ4T3_DOMAIN ); ?></a>
	</h2>

	<?php switch ( $tab ):

		case 'help':
			include_once 'credit-page.php';
			break;

		default:
			include_once 'general-settings.php';
			break;

	endswitch; ?>

</div><!-- /.wrap -->