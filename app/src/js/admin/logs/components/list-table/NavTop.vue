<template>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
            <select name="action" id="bulk-action-selector-top">
                <option value="-1">Bulk Actions</option>
                <option value="bulk_delete">Delete Selected</option>
                <option value="bulk_clean">Delete All</option>
                <option value="bulk_delete_all">Delete All (Keep redirects)</option>
            </select>
            <input type="submit" id="doaction" class="button action" value="Apply">
        </div>
        <div class="alignleft actions bulkactions">
            <select name="group_by_top" class="404_group_by">
                <option value="">Group by</option>
                <option value="url">404 Path</option>
                <option value="ref">From</option>
                <option value="ip">IP Address</option>
                <option value="ua">User Agent</option>
            </select>
            <input type="submit" name="filter_action" id="post-query" class="button" value="Apply">
        </div>
        <div class="tablenav-pages one-page">
            <span class="displaying-num">3 items</span>
            <span class="pagination-links">
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1" aria-describedby="table-paging">
                    <span class="tablenav-paging-text"> of <span class="total-pages">1</span></span>
                </span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
            </span>
        </div>
        <br class="clear">
    </div>
</template>

<script>
	export default {

		name: 'NavTop',

		data() {
			return {};
		}
	};
</script>

