<?php
/**
 * Logs top filters template.
 *
 * @var array  $filters Filters list.
 * @var string $label   Filters label.
 * @var string $current Currently active item.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Filters
 */

$i     = 1;
$count = count( $filters );

?>
<?php if ( ! empty( $filters ) ) : ?>
	<h2 class="screen-reader-text">
		<?php echo esc_html( $label ); ?>
	</h2>
	<ul class="subsubsub">
		<?php foreach ( $filters as $key => $filter ) : ?>
			<li class="<?php echo esc_html( $key ); ?>">
				<a
					class="<?php echo $key === $current ? esc_html( 'current' ) : ''; ?>"
					href="<?php echo esc_url( add_query_arg( 'filter', $key ) ); ?>"
				>
					<?php echo esc_html( $filter['label'] ); ?>
					<?php if ( isset( $filter['count'] ) ) : // Show count. ?>
						<span class="count">(<?php echo intval( $filter['count'] ); ?>)</span>
					<?php endif; ?>
				</a>
				<?php if ( $count !== $i ) : // Pipe separator is not required for last item. ?>
					|
				<?php endif; ?>
			</li>
			<?php $i ++; ?>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
