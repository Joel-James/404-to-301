<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Provide a admin menu view for the plugin
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
?>
<?php if ( ! empty( $tabs ) ) : ?>
	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $menu_key => $menu_title ) : ?>
			<a href="<?php echo add_query_arg( 'tab', $menu_key ); ?>" class="nav-tab <?php echo $menu_key === $tab ? 'nav-tab-active' : ''; ?>"><?php echo $menu_title; ?></a>
		<?php endforeach; ?>
	</h2>
<?php endif; ?>
