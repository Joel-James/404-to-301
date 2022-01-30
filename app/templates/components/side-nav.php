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
	<div class="wp-filter">
		<ul class="filter-links">
			<?php foreach ( $items as $key => $item ) : ?>
				<li>
					<a
						href="<?php echo esc_url( add_query_arg( 'tab', $key ) ); ?>"
						class="<?php echo $key === $current ? 'current' : ''; ?>"
					>
						<?php if ( ! empty( $item['icon'] ) ) : ?>
							<span class="dashicons dashicons-<?php echo esc_html( $item['icon'] ); ?> duckdev-dashicons-tabs"></span>
						<?php endif; ?>
						<?php echo esc_html( $item['title'] ); ?>
					</a>
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
