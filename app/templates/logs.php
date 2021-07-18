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


	<h2 class="screen-reader-text">Filter posts list</h2>
	<ul class="subsubsub">
		<li class="all"><a href="edit.php?post_type=post">All <span class="count">(7)</span></a> |</li>
		<li class="publish"><a href="edit.php?post_status=publish&amp;post_type=post">Redirects <span class="count">(7)</span></a></li>
	</ul>
	<form id="posts-filter" method="get">

		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input">Search Posts:</label>
			<input type="search" id="post-search-input" name="s" value="">
			<input type="submit" id="search-submit" class="button" value="Search Posts"></p>

		<input type="hidden" name="post_status" class="post_status_page" value="all">
		<input type="hidden" name="post_type" class="post_type_page" value="post">


		<input type="hidden" id="_wpnonce" name="_wpnonce" value="3b72a83c8b"><input type="hidden" name="_wp_http_referer" value="/incsub/single/wp-admin/edit.php?s&amp;post_status=all&amp;post_type=post&amp;action=-1&amp;m=202012&amp;cat=0&amp;filter_action=Filter&amp;paged=1&amp;action2=-1">
		<div class="tablenav top">

			<div class="alignleft actions bulkactions">
				<label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label><select name="action" id="bulk-action-selector-top">
					<option value="-1">Bulk actions</option>
					<option value="edit" class="hide-if-no-js">Edit</option>
					<option value="trash">Move to Trash</option>
				</select>
				<input type="submit" id="doaction" class="button action" value="Apply">
			</div>
			<div class="alignleft actions">
				<label for="filter-by-date" class="screen-reader-text">Filter by date</label>
				<select name="m" id="filter-by-date">
					<option value="0">All dates</option>
					<option selected="selected" value="202012">December 2020</option>
					<option value="202011">November 2020</option>
					<option value="202008">August 2020</option>
				</select>
				<label class="screen-reader-text" for="cat">Filter by category</label><select name="cat" id="cat" class="postform">
					<option value="0" selected="selected">All Categories</option>
					<option class="level-0" value="1">Uncategorized</option>
				</select>
				<input type="submit" name="filter_action" id="post-query-submit" class="button" value="Filter"></div>
			<div class="tablenav-pages one-page"><span class="displaying-num">3 items</span>
				<span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="paging-input"><label for="current-page-selector" class="screen-reader-text">Current Page</label><input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging"><span class="tablenav-paging-text"> of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
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

					<div class="hidden" id="inline_2105">
						<div class="post_title">Form</div>
						<div class="post_name">form</div>
						<div class="post_author">1</div>
						<div class="comment_status">open</div>
						<div class="ping_status">open</div>
						<div class="_status">publish</div>
						<div class="jj">03</div>
						<div class="mm">12</div>
						<div class="aa">2020</div>
						<div class="hh">06</div>
						<div class="mn">19</div>
						<div class="ss">55</div>
						<div class="post_password"></div>
						<div class="page_template">default</div>
						<div class="post_category" id="category_2105">1</div>
						<div class="tags_input" id="post_tag_2105"></div>
						<div class="sticky"></div>
						<div class="post_format"></div>
					</div>
					<div class="row-actions visible"><span class="edit"><a href="http://localhost/incsub/single/wp-admin/post.php?post=2105&amp;action=edit" aria-label="Edit “Form”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Form” inline" aria-expanded="true">Quick&nbsp;Edit</button> | </span><span class="trash"><a
								href="http://localhost/incsub/single/wp-admin/post.php?post=2105&amp;action=trash&amp;_wpnonce=a68489e119" class="submitdelete" aria-label="Move “Form” to the Trash">Trash</a> | </span><span class="view"><a href="http://localhost/incsub/single/form/" rel="bookmark" aria-label="View “Form”">View</a></span></div>
					<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
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
			<tr id="edit-2105" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-post inline-editor" style="">
				<td colspan="7" class="colspanchange">

					<fieldset class="inline-edit-col-left">
						<legend class="inline-edit-legend">Quick Edit</legend>
						<div class="inline-edit-col">


							<label>
								<span class="title">Title</span>
								<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value=""></span>
							</label>

							<label class="inline-edit-author"><span class="title">Author</span><select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
								</select></label>
							<label class="inline-edit-author"><span class="title">Author</span><select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
								</select></label>
							<label class="inline-edit-author"><span class="title">Author</span><select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
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
				<td class="title column-title has-row-actions column-primary page-title" data-colname="Title">
					<div class="locked-info"><span class="locked-avatar"></span> <span class="locked-text"></span></div>
					<strong><a class="row-title" href="http://localhost/incsub/single/wp-admin/post.php?post=2103&amp;action=edit" aria-label="“Quiz” (Edit)">Quiz</a></strong>

					<div class="hidden" id="inline_2103">
						<div class="post_title">Quiz</div>
						<div class="post_name">quiz</div>
						<div class="post_author">1</div>
						<div class="comment_status">open</div>
						<div class="ping_status">open</div>
						<div class="_status">publish</div>
						<div class="jj">03</div>
						<div class="mm">12</div>
						<div class="aa">2020</div>
						<div class="hh">06</div>
						<div class="mn">19</div>
						<div class="ss">36</div>
						<div class="post_password"></div>
						<div class="page_template">default</div>
						<div class="post_category" id="category_2103">1</div>
						<div class="tags_input" id="post_tag_2103"></div>
						<div class="sticky"></div>
						<div class="post_format"></div>
					</div>
					<div class="row-actions visible"><span class="edit"><a href="http://localhost/incsub/single/wp-admin/post.php?post=2103&amp;action=edit" aria-label="Edit “Quiz”">Edit</a> | </span><span class="inline hide-if-no-js"><button type="button" class="button-link editinline" aria-label="Quick edit “Quiz” inline" aria-expanded="false">Quick&nbsp;Edit</button> | </span><span class="trash"><a
								href="http://localhost/incsub/single/wp-admin/post.php?post=2103&amp;action=trash&amp;_wpnonce=bf0fcc3a0b" class="submitdelete" aria-label="Move “Quiz” to the Trash">Trash</a> | </span><span class="view"><a href="http://localhost/incsub/single/quiz/" rel="bookmark" aria-label="View “Quiz”">View</a></span></div>
					<button type="button" class="toggle-row"><span class="screen-reader-text">Show more details</span></button>
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
			<div class="tablenav-pages one-page"><span class="displaying-num">3 items</span>
				<span class="pagination-links"><span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
<span class="screen-reader-text">Current Page</span><span id="table-paging" class="paging-input"><span class="tablenav-paging-text">1 of <span class="total-pages">1</span></span></span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
<span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span></div>
			<br class="clear">
		</div>

	</form>


	<form method="get">
		<table style="display: none">
			<tbody id="inlineedit">
			<tr id="inline-edit" class="inline-edit-row inline-edit-row-post quick-edit-row quick-edit-row-post inline-edit-post" style="display: none">
				<td colspan="7" class="colspanchange">

					<fieldset class="inline-edit-col-left">
						<legend class="inline-edit-legend">Quick Edit</legend>
						<div class="inline-edit-col">


							<label>
								<span class="title">Title</span>
								<span class="input-text-wrap"><input type="text" name="post_title" class="ptitle" value=""></span>
							</label>

							<label class="inline-edit-author">
								<span class="title">Author</span>
								<select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
								</select>
							</label>
							<label class="inline-edit-author">
								<span class="title">Author</span>
								<select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
								</select>
							</label>
							<label class="inline-edit-author">
								<span class="title">Author</span>
								<select name="post_author" class="authors">
									<option value="4">Admin 2 (admin2)</option>
									<option value="5">Admin 3 (admin3)</option>
									<option value="6">Admin 3 Admin (admin--3)</option>
									<option value="2">Editor User (editor)</option>
									<option value="1">joel (joel)</option>
									<option value="3">Shop (shopmanager)</option>
								</select>
							</label>

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

			</tbody>
		</table>
	</form>

	<div id="ajax-response"></div>
	<div class="clear"></div>
</div>

