<template>
    <div class="tablenav-pages">
        <span class="displaying-num">{{ sprintf( $i18n.labels.items, totalItems ) }}</span>
        <span class="pagination-links" v-if="canPaginate">
            <button class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableFirstPage" @click="gotoFirst">«</button>
            <button class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disablePrevPage" @click="gotoPrev">‹</button>
            <span class="paging-input" v-if="isTop">
                <label for="current-page-selector" class="screen-reader-text">{{ $i18n.labels.current_page }}</label>
                <input @keyup.enter="gotoPage" class="current-page" min="1" :max="totalPages" id="current-page-selector" type="number" :value="page" size="1">
                <span class="tablenav-paging-text"> {{ $i18n.labels.of }} <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <span class="paging-input" v-else>
                <span class="tablenav-paging-text">{{ sprintf( $i18n.labels.page_of, page ) }} <span class="total-pages">{{ totalPages }}</span></span>
            </span>
            <button class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableNextPage" @click="gotoNext">›</button>
            <button class="tablenav-pages-navspan button" aria-hidden="true" :disabled="disableLastPage" @click="gotoLast">»</button>
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
				this.$emit( 'change', {
					page: page,
				} );
			},
		}
	};
</script>

