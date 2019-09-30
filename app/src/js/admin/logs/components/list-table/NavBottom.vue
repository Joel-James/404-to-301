<template>
    <div class="tablenav bottom">
        <BulkAction
                v-if="hasBulkActions"
                action-key="bulk-actions"
                action-label="Bulk Actions"
                :action-options="bulkActions"
                :is-top="false"
        />
        <Pagination
                :total-items="totalItems"
                :current-page="currentPage"
                :per-page="perPage"
                :is-top="false"
                :pagination-callback="paginationCallback"
        />
        <br class="clear">
    </div>
</template>

<script>
	import BulkAction from './BulkAction.vue'
	import Pagination from './Pagination.vue'

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Settings',

		/**
		 * Required components in this component.
		 *
		 * @since 4.0.0
		 */
		components: {
			BulkAction, Pagination
		},

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
			totalItems: {
				type: Number,
				default: 0,
			},
			perPage: {
				type: Number,
				default: 20,
			},
			currentPage: {
				type: Number,
				default: 1,
			},
			paginationCallback: {
				type: Function,
				required: false,
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
			}
		},

		data() {
			return {
				bulkAction: -1,
			}
		}
	};
</script>

<style lang="css" scoped>
</style>