<?php
/**
 * Navigation menu template.
 *
 * @since      4.0.0
 *
 * @var string $base_url    Base URL.
 * @var string $current_tab Current menu item.
 * @var array  $tab_items   Nav menu items.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2025, Joel James
 * @package    View
 */

?>
<h2 class="nav-tab-wrapper">
	<?php foreach ( $tab_items as $tab_key => $tab_item ) : ?>
		<a
			href="<?php echo esc_url( add_query_arg( 'tab', $tab_key, $base_url ) ); ?>"
			class="nav-tab <?php echo $tab_key === $current_tab ? 'nav-tab-active' : ''; ?>"
		>
			<?php if ( ! empty( $tab_item['icon'] ) ) : ?>
				<span class="dashicons <?php echo esc_html( $tab_item['icon'] ); ?>"></span>
			<?php endif; ?>
			<?php echo esc_html( $tab_item['label'] ); ?>
		</a>
	<?php endforeach; ?>
</h2>
