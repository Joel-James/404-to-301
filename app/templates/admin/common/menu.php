<?php

// Direct hit? Rest in peace..
defined( 'WPINC' ) || die;

/**
 * Provide a admin menu view for the plugin
 *
 * @var array  $tabs Tabs list.
 * @var string $tab  Current tab.
 *
 * @author Joel James <me@joelsays.com>
 * @link   https://duckdev.com
 * @since  4.0
 *
 */
?>
<?php if ( ! empty( $tabs ) ) : ?>
    <h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $menu_key => $menu ) : ?>
            <a href="<?php echo add_query_arg( 'tab', $menu_key ); ?>" class="nav-tab <?php echo $menu_key === $tab ? 'nav-tab-active' : ''; ?>">
				<?php if ( ! empty( $menu['icon'] ) ) : ?>
                    <span class="dashicons <?php echo esc_attr( $menu['icon'] ); ?>"></span>
				<?php endif; ?>
				<?php echo $menu['label']; ?>
            </a>
		<?php endforeach; ?>
    </h2>
<?php endif; ?>
