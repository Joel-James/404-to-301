<?php
/**
 * Pagination template.
 *
 * @var int $total   Total items.
 * @var int $current Current page.
 * @var int $pages   Total pages.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2021, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage Search
 */

// Get label.
$label = empty( $label ) ? __( 'Search', '404-to-301' ) : $label;

?>
<?php if ( $total > 0 ) : ?>
	<div class="tablenav-pages">
		<span class="displaying-num"><?php echo intval( $total ); ?> items</span>
		<span class="pagination-links">
		<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
		<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
		<span class="paging-input">
			<label for="current-page-selector" class="screen-reader-text">
				<?php esc_html_e( 'Current Page', '404-to-301' ); ?>
			</label>
			<input
				class="current-page"
				id="current-page-selector"
				type="text"
				name="paged"
				value="1"
				size="1"
				aria-describedby="table-paging"
			>
			<span class="tablenav-paging-text"> of <span class="total-pages"><?php echo intval( $pages ); ?></span></span>
		</span>
		<a class="next-page button" href="">
			<span class="screen-reader-text"><?php esc_html_e( 'Next page', '404-to-301' ); ?></span>
			<span aria-hidden="true">›</span>
		</a>
		<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
	</span>
	</div>
<?php endif; ?>
