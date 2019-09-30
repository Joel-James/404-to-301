<template>
    <div class="tablenav-pages">
        <span class="displaying-num">{{ totalItems }} items</span>
        <span class="pagination-links" v-if="canPaginate">
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableFirstPage" v-on:click="paginate(1)">«</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disablePrevPage" v-on:click="paginate(prevPage)">‹</span>
            <span class="paging-input" v-if="isTop">
                <label for="current-page-selector" class="screen-reader-text">Current Page</label>
                <input class="current-page" min="1" :max="totalPages" id="current-page-selector" type="text" name="paged" :value="currentPageNumber" size="1">
                <span class="tablenav-paging-text"> of <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="paging-input" v-else>
                <span class="tablenav-paging-text">{{ currentPageNumber }} of <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableNextPage" v-on:click="paginate(nextPage)">›</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" v-bind:disabled="disableLastPage" v-on:click="paginate( totalPages )">»</span>
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
				return ( this.totalPages - this.currentPageNumber ) <= 1;
			},

			/**
			 * Calculate if first page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disableFirstPage() {
				return this.currentPageNumber <= 2;
			},

			/**
			 * Calculate if next page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disableNextPage() {
				return this.totalPages <= this.currentPageNumber;
			},

			/**
			 * Calculate if previous page should be disabled.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			disablePrevPage() {
				return this.currentPageNumber <= 1;
			},

			/**
			 * Check if we can paginate.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			canPaginate() {
				return this.perPage < this.totalItems;
			}
		},

		data() {
			return {
				bulkAction: -1,
				prevPage: 0,
				nextPage: 2,
				currentPageNumber: this.currentPage
			}
		},

		methods: {
			paginate( page ) {
				if ( page > this.totalPages || page < 1 ) {
					return;
				}

				this.nextPage = page + 1;
				this.prevPage = page - 1;
				this.currentPageNumber = page;

				if ( this.paginationCallback ) {
                    this.paginationCallback( page );
                }
			}
		}
	};
</script>

