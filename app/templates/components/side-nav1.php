<?php
/**
 * Side navigation template.
 *
 * @var array  $items   Menu list.
 * @var string $current Current tab.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Navigation
 */

?>

<?php if ( ! empty( $items ) ) : ?>
	<div class="duckdev-nav-tab-wrapper" id="general-tabs-wrapper">
		<?php foreach ( $items as $key => $item ) : ?>
			<div class="duckdev-tab">
				<a
					href="<?php echo esc_url( add_query_arg( 'tab', $key ) ); ?>"
					class="duckdev-nav-tab <?php echo $key === $current ? 'duckdev-active-tab' : ''; ?>"
				>
					<?php if ( ! empty( $item['icon'] ) ) : ?>
						<span class="dashicons dashicons-<?php echo esc_html( $item['icon'] ); ?> duckdev-dashicons-tabs"></span>
					<?php endif; ?>
					<span class="duckdev-nav-desktop"><?php echo esc_html( $item['title'] ); ?></span>
				</a>
			</div>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
