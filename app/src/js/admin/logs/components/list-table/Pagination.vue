<template>
    <div class="tablenav-pages">
        <span class="displaying-num">{{ totalItems }} items</span>
        <span class="pagination-links">
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableFirstPage">«</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disablePrevPage">‹</span>
            <span class="paging-input" v-if="isTop">
                <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                <input class="current-page" id="current-page-selector" type="text" name="paged" :value="currentPage" size="1">
                <span class="tablenav-paging-text"> of <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="paging-input" v-else>
                <span class="tablenav-paging-text">{{ currentPage }} of <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableNextPage">›</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableLastPage">»</span>
        </span>
    </div>
</template>

<script>
	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Pagination',

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			totalItems: {
				type: Number,
				required: true,
			},
			perPage: {
				type: Number,
				required: true,
			},
			currentPage: {
				type: Number,
				required: true,
			},
			isTop: {
				type: Boolean,
				default: true
			},
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
			 * Calculate the total no. of pages.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			totalPages() {
				return Math.ceil( this.totalItems / this.perPage );
			},

			/**
			 * Calculate if last page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
            disableLastPage() {
				return ( this.totalPages - this.currentPage ) <= 1;
            },

			/**
			 * Calculate if first page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disableFirstPage() {
				return ( this.totalPages - this.currentPage ) > 1;
			},

			/**
			 * Calculate if next page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disableNextPage() {
				return this.totalPages <= this.currentPage;
			},

			/**
			 * Calculate if previous page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disablePrevPage() {
				return this.currentPage > this.totalPages;
			}
		},

		data() {
			return {
				bulkAction: -1,
			}
		}
	};
</script>

