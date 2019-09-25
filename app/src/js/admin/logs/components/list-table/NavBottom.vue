<template>
    <div class="tablenav bottom">
        <div class="alignleft actions bulkactions">
            <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label><select name="action2" id="bulk-action-selector-bottom">
            <option value="-1">Bulk Actions</option>
            <option value="edit" class="hide-if-no-js">Edit</option>
            <option value="trash">Move to Trash</option>
        </select>
            <input type="submit" id="doaction2" class="button action" value="Apply">
        </div>
        <div class="alignleft actions">
        </div>
        <div class="tablenav-pages"><span class="displaying-num">4 items</span>
            <span class="pagination-links">
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <span class="screen-reader-text">Current Page</span>
                <span id="table-paging" class="paging-input">
                    <span class="tablenav-paging-text">1 of <span class="total-pages">2</span></span>
                </span>
                <a class="next-page button" href="//localhost:3000/development/wordpress/wp_404/wp-admin/edit.php?post_type=page&amp;paged=2">
                    <span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span>
                </a>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
            </span>
        </div>
        <br class="clear">
    </div>
</template>

<script>
export default {

    name: 'Settings',

    data () {
        return {

        };
    }
};
</script>

<style lang="css" scoped>
</style>