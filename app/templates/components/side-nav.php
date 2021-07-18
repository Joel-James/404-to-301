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
	<h2 class="nav-tab-wrapper">
		<div class="nav-tab-container">
			<?php foreach ( $items as $key => $item ) : ?>
				<a
					href="<?php echo esc_url( $item['url'] ); ?>"
					class="nav-tab <?php echo $key === $current ? 'nav-tab-active' : ''; ?>"
					aria-current="<?php echo $key === $current ? 'page' : 'false'; ?>"
				>
					<?php if ( ! empty( $item['icon'] ) ) : ?>
						<span class="dashicons dashicons-<?php echo esc_html( $item['icon'] ); ?>"></span>
					<?php endif; ?>
					<?php echo esc_html( $item['title'] ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</h2>
<?php endif; ?>
