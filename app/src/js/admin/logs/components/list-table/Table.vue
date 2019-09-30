<template>
    <form method="post">
        <NavTop
                :bulk-actions="bulkActions"
                :extra-actions="extraActions"
                :per-page="perPage"
                :current-page="currentPage"
                :total-items="totalItems"
                :pagination-callback="paginationCallback"
        />
        <table :class="tableClass">
            <Header :columns="columns" :show-cb="showCb"/>
            <tbody>
            <Row v-for="row in rows" :row="row" :id="row.id" :columns="columns" :show-cb="showCb"/>
            </tbody>
        </table>
        <NavBottom
                :bulk-actions="bulkActions"
                :per-page="perPage"
                :current-page="currentPage"
                :total-items="totalItems"
                :pagination-callback="paginationCallback"
        />
    </form>
</template>

<script>
	import Header from './Header.vue'
	import Row from './Row.vue'
	import NavTop from './NavTop.vue'
	import NavBottom from './NavBottom.vue'

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Table',

		/**
		 * Required components in this component.
		 *
		 * @since 4.0.0
		 */
		components: {
			Header, Row, NavTop, NavBottom
		},

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			columns: {
				type: Object,
				required: true,
				default: {},
			},
			rows: {
				type: Array,
				required: true,
				default: [],
			},
			tableClass: {
				type: String,
				default: 'wp-list-table widefat fixed striped',
			},
			totalItems: {
				type: Number,
				default: 101,
			},
			perPage: {
				type: Number,
				default: 20,
			},
			currentPage: {
				type: Number,
				default: 1,
			},
			sortBy: {
				type: String,
				default: null,
			},
			sortOrder: {
				type: String,
				default: 'asc',
			},
			showCb: {
				type: Boolean,
				default: true,
			},
			bulkActions: {
				type: Array,
				required: false,
				default: [],
			},
			extraActions: {
				type: Array,
				required: false,
				default: []
			},
			paginationCallback: {
				type: Function,
				required: false,
			}
		},
	};
</script>