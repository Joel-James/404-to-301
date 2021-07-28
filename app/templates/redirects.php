<?php
/**
 * Admin settings page base template.
 *
 * @var array  $tabs    Tabs list.
 * @var string $current Current tab.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @subpackage Pages
 */

?>

<div class="wrap duckdev-metaboxes">
	<div class="duckdev-notice-wrap">
		<?php
		/**
		 * Action hook to print settings notices.
		 *
		 * @param string $page Current page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd404_admin_notices', 'redirects' );
		?>
	</div>
</div>

