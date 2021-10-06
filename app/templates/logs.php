<?php
/**
 * Admin settings page base template.
 *
 * @var array $filters        Filters.
 * @var array $bulk_actions   Bulk actions.
 * @var array $filter_actions Filter actions.
 * @var array $pagination     Pagination data.
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

<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( '404 Logs', '404-to-301' ); ?>
	</h1>
	<hr class="wp-header-end">

	<div class="duckdev-notice-wrap">
		<?php
		/**
		 * Action hook to print settings notices.
		 *
		 * @param string $page Current page.
		 *
		 * @since 4.0.0
		 */
		do_action( 'dd4t3_admin_notices', 'logs' );
		?>
	</div>

	<?php $this->render( 'components/top-filters', $filters ); // Render top filters. ?>

	<form id="logs-filter" method="get">

		<?php $this->render( 'components/search-box' ); // Search box. ?>

		<input type="hidden" name="post_status" class="post_status_page" value="all">

		<div class="tablenav top">

			<?php $this->render( 'components/bulk-actions', array( 'actions' => $bulk_actions ) ); // Render bulk actions. ?>
			<?php $this->render( 'components/filter-actions', array( 'actions' => $filter_actions ) ); // Render extra filters. ?>

			<?php $this->render( 'components/pagination', $pagination ); // Render pagination. ?>

			<br class="clear">
		</div>
		<h2 class="screen-reader-text">Posts list</h2>
		<table class="wp-list-table widefat fixed striped table-view-excerpt posts">
			<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column">
					<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
					<input id="cb-select-all-1" type="checkbox">
				</td>
				<th scope="col" class="manage-column column-url sortable desc">
					<a href="">
						<span>404 URL</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column column-date">
					<a href="">
						<span>Date</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column column-referrer">Referrer</th>
				<th scope="col" class="manage-column column-ip">IP Address</th>
				<th scope="col" class="manage-column column-method">Method</th>
				<th scope="col" class="manage-column column-ua num sortable desc">
					<a href="">
						<span>User Agent</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column column-actions">Actions</th>
			</tr>
			</thead>

			<tbody id="the-list">
			<tr id="post-2103" class="iedit author-self level-0 post-2103 type-post status-publish format-standard hentry category-uncategorized">
				<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-2103">
						Select Quiz </label>
					<input id="cb-select-2103" type="checkbox" name="post[]" value="2103">
					<div class="locked-indicator">
						<span class="locked-indicator-icon" aria-hidden="true"></span>
						<span class="screen-reader-text">
				“Quiz” is locked				</span>
					</div>
				</th>
				<td class="title column-url has-row-actions" data-colname="Title">
					<a class="row-title" href="" aria-label="“Quiz” (Edit)">
						/hello-world-error
					</a>
					<div class="row-actions">
						<span class="inline">
							<button type="button" class="button-link editinline" aria-label="Quick edit “Quiz” inline" aria-expanded="false">Exclude URL</button> |
						</span>
						<span class="delete">
							<a href="" class="submitdelete" aria-label="Move “Quiz” to the Trash">Delete</a>
						</span>
					</div>
					<button type="button" class="toggle-row">
						<span class="screen-reader-text">Show more details</span>
					</button>
				</td>
				<td class="author column-date" data-colname="Author">
					26 Feb 2021
					<br/>
					10:36 AM
				</td>
				<td class="categories column-referrer" data-colname="Categories">
					<a href="">http://localhost/incsub/single/wp-admin/admin.php?page=404-to-301-logs</a>
				</td>
				<td class="tags column-ip" data-colname="Tags">
					<a href="">
						127.0.0.1
					</a>
				</td>
				<td class="comments column-method" data-colname="Comments">GET</td>
				<td class="column-ua">Mozilla/5.0(Windows NT 6.3; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0</td>
				<td class="date column-actions" data-colname="Date">
					<a href="" title="Customize">
						<span class="dashicons dashicons-admin-generic"></span>
						<span class="screen-reader-text">Customize</span>
					</a>
					<a href="" title="Customize">
						<span class="dashicons dashicons-hidden"></span>
						<span class="screen-reader-text">Exclude URL</span>
					</a>
					<a href="" title="Customize">
						<span class="dashicons dashicons-trash"></span>
						<span class="screen-reader-text">Delete Log</span>
					</a>
				</td>
			</tr>
			</tbody>

			<tfoot>
			<tr>
				<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td>
				<th scope="col" class="manage-column column-url sortable desc">
					<a href="">
						<span>404 URL</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column column-date">
					<a href="">
						<span>Date</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" class="manage-column column-referrer">Referrer</th>
				<th scope="col" class="manage-column column-ip">IP Address</th>
				<th scope="col" class="manage-column column-method num">Method</th>
				<th scope="col" class="manage-column column-ua sortable asc">
					<a href="">
						<span>User Agent</span>
						<span class="sorting-indicator"></span>
					</a>
				</th>
				<th scope="col" id="date" class="manage-column column-actions">Actions</th>
			</tr>
			</tfoot>

		</table>
		<div class="tablenav bottom">

			<?php
			// Render bulk actions.
			$this->render(
				'components/bulk-actions',
				array(
					'bottom'  => true,
					'actions' => $bulk_actions,
				)
			);
			?>

			<div class="tablenav-pages"><span class="displaying-num">7 items</span>
				<span class="pagination-links">
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
					<span class="screen-reader-text">Current Page</span>
					<span id="table-paging" class="paging-input">
						<span class="tablenav-paging-text">1 of <span class="total-pages">2</span></span>
					</span>
					<a class="next-page button" href="">
						<span class="screen-reader-text">Next page</span>
						<span aria-hidden="true">›</span>
					</a>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
				</span>
			</div>

			<br class="clear">
		</div>

	</form>

	<div id="ajax-response"></div>
	<div class="clear"></div>
</div>

