<template>
    <div class="tablenav top">
        <div class="alignleft actions bulkactions" v-if="hasBulkActions">
            <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
            <select name="action" id="bulk-action-selector-top" v-model="bulkAction">
                <option value="-1">Bulk Actions</option>
                <option v-for="action in bulkActions" :value="action.key">{{ action.label }}</option>
            </select>
            <input type="submit" id="doaction" class="button action" value="Apply" v-if="hasBulkActions">
        </div>
        <div class="alignleft actions bulkactions" v-if="hasExtraActions" v-for="action in extraActions">
            <select name="group_by_top" class="404_group_by">
                <option value="">Group by</option>
                <option value="url">404 Path</option>
                <option value="ref">From</option>
                <option value="ip">IP Address</option>
                <option value="ua">User Agent</option>
            </select>
            <input type="submit" name="filter_action" id="post-query" class="button" value="Apply">
        </div>
        <div class="tablenav-pages">
            <span class="displaying-num">4 items</span>
            <span class="pagination-links">
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
                <span class="paging-input">
                    <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                    <input class="current-page" id="current-page-selector" type="text" name="paged" value="1" size="1"
                           aria-describedby="table-paging">
                    <span class="tablenav-paging-text"> of <span class="total-pages">2</span></span>
                </span>
                <a class="next-page button"
                   href="//localhost:3000/development/wordpress/wp_404/wp-admin/edit.php?post_type=page&amp;paged=2">
                    <span class="screen-reader-text">Next page</span>
                    <span aria-hidden="true">›</span>
                </a>
                <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span></span>
        </div>
        <br class="clear">
    </div>
</template>

<script>
	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'NavTop',

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			bulkActions: {
				type: Array,
				required: false,
				default: [],
			},
			extraActions: {
				type: Array,
                required: false,
                default: []
            }
		},

		/**
		 * Dynamic methods to handle table.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
        computed: {
			/**
			 * Is there bulk actions available.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			hasBulkActions() {
				return this.bulkActions.length > 0;
			},

			/**
			 * Is there extra actions available.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			hasExtraActions() {
				return this.extraActions.length > 0;
			},
        },

        data() {
        	return {
				bulkAction: -1,
            }
        }
	};
</script>

