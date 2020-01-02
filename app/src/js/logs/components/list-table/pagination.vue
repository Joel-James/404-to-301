<template>
    <div class="tablenav-pages">
        <span class="displaying-num">{{ sprintf( __( '%d items', '404-to-301' ), totalItems ) }}</span>
        <span class="pagination-links" v-if="canPaginate">
            <span class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableFirstPage" @click="gotoFirst">«</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disablePrevPage" @click="gotoPrev">‹</span>
            <span class="paging-input" v-if="isTop">
                <label for="current-page-selector" class="screen-reader-text">{{ __( 'Current Page', '404-to-301' ) }}</label>
                <input @keyup.enter="gotoPage" class="current-page" min="1" :max="totalPages" id="current-page-selector" type="number" :value="page" size="1">
                <span class="tablenav-paging-text"> {{ __( 'of', '404-to-301' ) }} <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="paging-input" v-else>
                <span class="tablenav-paging-text">{{ sprintf( __( '%d of', '404-to-301' ), page ) }} <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableNextPage" @click="gotoNext">›</span>
            <span class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableLastPage" @click="gotoLast">»</span>
        </span>
    </div>
</template>

<script>
	export default {
		name: 'Pagination',

		props: {
			totalItems: {
				type: Number,
				required: true,
			},
			perPage: {
				type: Number,
				required: true,
			},
			page: {
				type: Number,
				required: true,
			},
			isTop: {
				type: Boolean,
				default: true
			},
		},

		computed: {
			prevPage() {
				if ( this.page < 1 ) {
					return 0;
				}

				return this.page - 1;
			},

			nextPage() {
				return this.page + 1;
			},

			totalPages() {
				return Math.ceil( this.totalItems / this.perPage );
			},

			disableLastPage() {
				return ( this.totalPages - this.page ) <= 1;
			},

			disableFirstPage() {
				return this.page <= 2;
			},

			disableNextPage() {
				return this.totalPages <= this.page;
			},

			disablePrevPage() {
				return this.page <= 1;
			},

			canPaginate() {
				return this.perPage < this.totalItems;
			}
		},

		methods: {
			gotoNext() {
				this.paginate( this.nextPage )
			},

			gotoPrev() {
				this.paginate( this.prevPage )
			},

			gotoFirst() {
				this.paginate( 1 )
			},

			gotoLast() {
				this.paginate( this.totalPages )
			},

			gotoPage( event ) {
				this.paginate( parseInt( event.target.value ) )
            },

			paginate( page ) {
				if ( page > this.totalPages ) {
					page = this.totalPages;
				} else if ( page < 1 ) {
					page = 1;
                }

				// Execute pagination event.
				this.$root.$emit( 'listTablePaginationChange', {
					page: page,
				} );
			},
		}
	};
</script>

