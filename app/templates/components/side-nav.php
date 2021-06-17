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
	<aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
		<nav class="space-y-1">
			<?php foreach ( $items as $key => $item ) : ?>
				<?php $class = $key === $current ? 'text-gray-900 hover:text-gray-900 bg-gray-50 hover:bg-white' : 'hover:bg-gray-50 hover:text-gray-600'; ?>
				<a
						href="<?php echo esc_url( $item['url'] ); ?>"
						class="<?php echo esc_html( $class ); ?> focus:shadow-none group rounded-md px-3 py-2 flex items-center text-sm font-medium"
						aria-current="<?php $class = $key === $current ? 'page' : 'false'; ?>"
				>
					<?php $this->render_icon( $item['icon'] ); // Render svg icons. ?>
					<span class="truncate"><?php echo esc_html( $item['title'] ); ?></span>
				</a>
			<?php endforeach; ?>
		</nav>
	</aside>
<?php endif; ?>
