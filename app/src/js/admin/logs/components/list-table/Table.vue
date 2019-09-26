<template>
    <form method="post">
        <NavTop :bulkActions="bulkActions"/>
        <table :class="tableClass">
            <Header :columns="columns" :showCb="showCb"/>
            <tbody>
            <Row v-for="row in rows" :row="row" :id="row.id" :columns="columns" :showCb="showCb"/>
            </tbody>
        </table>
        <NavBottom/>
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
				default: 0,
			},
			totalPages: {
				type: Number,
				default: 1,
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
		},
	};
</script>