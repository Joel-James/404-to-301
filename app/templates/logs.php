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

<div class="wrap">
	<h1 class="wp-heading-inline">404 Logs</h1>
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
		do_action( 'dd404_admin_notices', 'logs' );
		?>
	</div>

	<h2 class="screen-reader-text">Filter posts list</h2>
	<ul class="subsubsub">
		<li class="all"><a href="">All <span class="count">(7)</span></a> |</li>
		<li class="publish"><a href="">Redirects <span class="count">(7)</span></a></li>
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
				<td></td>
			</tr>
			<tr class="hidden"></tr>
			<tr id="edit-2105" class="inline-edit-row inline-edit-row-page quick-edit-row quick-edit-row-page inline-edit-page inline-editor" style="">
				<td colspan="8" class="colspanchange">

					<fieldset class="inline-edit-col-left">
						<legend class="inline-edit-legend">Customize</legend>
						<div class="inline-edit-col">


							<label class="inline-edit-author"><span class="title">Redirect</span><select name="post_author" class="authors">
									<option value="4">Global</option>
									<option value="5">Enable</option>
									<option value="6">Disable</option>
								</select></label>

							<label class="inline-edit-author"><span class="title">Logs</span><select name="post_author" class="authors">
									<option value="4">Global</option>
									<option value="5">Enable</option>
									<option value="6">Disable</option>
								</select></label>
							<label class="inline-edit-author"><span class="title">Email</span><select name="post_author" class="authors">
									<option value="4">Global</option>
									<option value="5">Enable</option>
									<option value="6">Disable</option>
								</select></label>

						</div>
					</fieldset>
					<fieldset class="inline-edit-col-right">
						<div class="inline-edit-col">


							<label>
								<span class="title">Redirect URL</span>
								<span class="input-text-wrap"><input type="url" name="post_title" class="ptitle regular-text code" value=""></span>
							</label>

							<label class="inline-edit-author"><span class="title">Redirect Type</span><select name="post_author" class="authors">
									<option value="4">301 Redirect</option>
									<option value="5">302 Redirect</option>
									<option value="6">307 Redirect</option>
								</select></label>

						</div>
					</fieldset>

					<div class="submit inline-edit-save">
						<button type="button" class="button cancel alignleft">Cancel</button>

						<input type="hidden" id="_inline_edit" name="_inline_edit" value="144f7f1473">
						<button type="button" class="button button-primary save alignright">Update</button>
						<span class="spinner"></span>

						<input type="hidden" name="post_view" value="excerpt">
						<input type="hidden" name="screen" value="edit-post">
						<br class="clear">

						<div class="notice notice-error notice-alt inline hidden">
							<p class="error"></p>
						</div>
					</div>

				</td>
			</tr>
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

