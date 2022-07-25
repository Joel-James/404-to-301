<?php
/**
 * Admin settings page base template.
 *
 * @var array  $tabs    Tabs list.
 * @var string $current Current tab.
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

<div class="wrap dd4t3-redirects-wrap">
	<h1 class="wp-heading-inline">Redirects</h1>
	<a href="" class="page-title-action">Add New</a>
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

	<h2 class="screen-reader-text">Filter posts list</h2>
	<ul class="subsubsub">
		<li class="all"><a href="">All <span class="count">(7)</span></a> |</li>
		<li class="publish"><a href="">Disabled <span class="count">(2)</span></a></li>
		<li class="publish"><a href="">Ignored <span class="count">(1)</span></a></li>
	</ul>
	<form id="posts-filter" method="get">

		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">Search Posts:</label>
			<input type="search" id="post-search-input" name="s" value="">
			<input type="submit" id="search-submit" class="button" value="Search Posts">
		</p>

		<input type="hidden" name="post_status" class="post_status_page" value="all">
		<input type="hidden" name="post_type" class="post_type_page" value="post">

		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
				<select name="action" id="bulk-action-selector-top">
					<option value="-1">Bulk actions</option>
					<option value="delete" class="hide-if-no-js">Delete</option>
				</select>
				<input type="submit" class="button action" value="Apply">
			</div>
			<div class="alignleft actions">
				<label class="screen-reader-text" for="cat">Group by</label>
				<select name="cat" id="cat" class="postform">
					<option value="0" selected="selected">Group</option>
					<option class="level-0" value="1">404 Path</option>
					<option class="level-0" value="1">Referer</option>
					<option class="level-0" value="1">User Agent</option>
					<option class="level-0" value="1">IP Address</option>
				</select>
				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter"></div>

			<div class="tablenav-pages">
				<span class="displaying-num">7 items</span>
				<span class="pagination-links">
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
					<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
					<span class="paging-input">
						<label for="current-page-selector" class="screen-reader-text">Current Page</label>
						<input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging">
						<span class="tablenav-paging-text"> of <span class="total-pages">2</span></span>
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
		<h2 class="screen-reader-text">Posts list</h2>
		<table class="wp-list-table widefat fixed striped table-view-excerpt posts">
			<thead>
			<tr>
				<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>
				<th scope="col" id="title" class="manage-column column-title column-primary sortable desc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="author" class="manage-column column-author">Author</th>
				<th scope="col" id="categories" class="manage-column column-categories">Categories</th>
				<th scope="col" id="tags" class="manage-column column-tags">Tags</th>
				<th scope="col" id="comments" class="manage-column column-comments num sortable desc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span></span><span class="sorting-indicator"></span></a></th>
				<th scope="col" id="date" class="manage-column column-date sortable asc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a></th>
			</tr>
			</thead>

			<tbody id="the-list">
			<tr id="post-2105" class="iedit author-self level-0 post-2105 type-post status-publish format-standard hentry category-uncategorized" style="display: none;">
				<th scope="row" class="check-column"><label class="screen-reader-text" for="cb-select-2105">
						Select Form </label>
					<input id="cb-select-2105" type="checkbox" name="post[]" value="2105">
					<div class="locked-indicator">
						<span class="locked-indicator-icon" aria-hidden="true"></span>
						<span class="screen-reader-text">
				“Form” is locked				</span>
					</div>
				</th>
				<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
					<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
					<strong><a class="row-title" href="http://localhost/incsub/single/wp-admin/post.php?post=2105&amp;action=edit" aria-label="“Form” (Edit)">Form</a></strong>

					<div class="row-actions visible">
						<span class="view">
							<a href="" rel="bookmark" aria-label="View “Quiz”">View</a> |
						</span>
						<span class="inline hide-if-no-js">
							<button type="button" class="button-link editinline" aria-label="Quick edit “Quiz” inline" aria-expanded="false">Configure</button> |
						</span>
						<span class="delete">
							<a href="" class="submitdelete" aria-label="Move “Quiz” to the Trash">Delete</a>
						</span>
					</div>
					<button type="button" class="toggle-row">
						<span class="screen-reader-text">Show more details</span>
					</button>
				</td>
				<td class="author column-author" data-colname="Author"><a href="edit.php?post_type=post&amp;author=1">joel</a></td>
				<td class="categories column-categories" data-colname="Categories"><a href="edit.php?category_name=uncategorized">Uncategorized</a></td>
				<td class="tags column-tags" data-colname="Tags"><span aria-hidden="true">—</span><span class="screen-reader-text">No tags</span></td>
				<td class="comments column-comments" data-colname="Comments">
					<div class="post-com-count-wrapper">
						<span aria-hidden="true">—</span><span class="screen-reader-text">No comments</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">No comments</span></span></div>
				</td>
				<td class="date column-date" data-colname="Date">Published<br>2020/12/03 at 6:19 am</td>
			</tr>
			<tr class="hidden"></tr>
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
				<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
					<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
					<strong><a class="row-title" href="http://localhost/incsub/single/wp-admin/post.php?post=2103&amp;action=edit" aria-label="“Quiz” (Edit)">/hello-world-error</a></strong>

					<div class="row-actions visible">
						<span class="view">
							<a href="" rel="bookmark" aria-label="View “Quiz”">View</a> |
						</span>
						<span class="inline hide-if-no-js">
							<button type="button" class="button-link editinline" aria-label="Quick edit “Quiz” inline" aria-expanded="false">Configure</button> |
						</span>
						<span class="delete">
							<a href="" class="submitdelete" aria-label="Move “Quiz” to the Trash">Delete</a>
						</span>
					</div>
					<button type="button" class="toggle-row">
						<span class="screen-reader-text">Show more details</span>
					</button>
				</td>
				<td class="author column-author" data-colname="Author"><a href="edit.php?post_type=post&amp;author=1">joel</a></td>
				<td class="categories column-categories" data-colname="Categories"><a href="edit.php?category_name=uncategorized">Uncategorized</a></td>
				<td class="tags column-tags" data-colname="Tags"><span aria-hidden="true">—</span><span class="screen-reader-text">No tags</span></td>
				<td class="comments column-comments" data-colname="Comments">
					<div class="post-com-count-wrapper">
						<span aria-hidden="true">—</span><span class="screen-reader-text">No comments</span><span class="post-com-count post-com-count-pending post-com-count-no-pending"><span class="comment-count comment-count-no-pending" aria-hidden="true">0</span><span class="screen-reader-text">No comments</span></span></div>
				</td>
				<td class="date column-date" data-colname="Date">Published<br>2020/12/03 at 6:19 am</td>
			</tr>
			</tbody>

			<tfoot>
			<tr>
				<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td>
				<th scope="col" class="manage-column column-title column-primary sortable desc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=title&amp;order=asc"><span>Title</span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-author">Author</th>
				<th scope="col" class="manage-column column-categories">Categories</th>
				<th scope="col" class="manage-column column-tags">Tags</th>
				<th scope="col" class="manage-column column-comments num sortable desc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=comment_count&amp;order=asc"><span><span class="vers comment-grey-bubble" title="Comments"><span class="screen-reader-text">Comments</span></span></span><span class="sorting-indicator"></span></a></th>
				<th scope="col" class="manage-column column-date sortable asc"><a href="http://localhost/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;action2=-1&amp;orderby=date&amp;order=desc"><span>Date</span><span class="sorting-indicator"></span></a></th>
			</tr>
			</tfoot>

		</table>
		<div class="tablenav bottom">

			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
					<option value="-1">Bulk actions</option>
					<option value="edit" class="hide-if-no-js">Edit</option>
					<option value="trash">Move to Trash</option>
				</select>
				<input type="submit" id="doaction2" class="button action" value="Apply">
			</div>
			<div class="alignleft actions">
			</div>

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

