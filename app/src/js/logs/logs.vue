<template>
    <div id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">

                    <search-box id="log-search-input"
                                label="Search URL"
                                v-model="search"
                                @input="searchUrl"
                                @submitSearch="searchUrl"
                    />

                    <div class="tablenav top">

                        <bulk-action action-key="bulk-actions"
                                     action-label="Bulk Actions"
                                     :action-options="bulkActions"
                                     @submit="bulkActionSubmit"
                                     @change="bulkActionChange"
                        />

                        <bulk-action v-for="action in extraActions"
                                     :action-label="action.label"
                                     :action-options="action.options"
                                     :action-key="action.key"
                                     :enable-submit="false"
                                     :key="action.key"
                                     @change="bulkActionChange"
                        />

                        <span v-if="waiting" class="table-loader">
                            <span class="spinner is-active"></span>
                        </span>

                        <pagination :total-items="total"
                                    :page="filters.page"
                                    :per-page="filters.per_page"
                                    @change="updatePagination"
                        />
                        <br class="clear">

                    </div>

                    <table class="wp-list-table widefat fixed striped">

                        <thead>

                        <table-title-row :columns="columns"
                                         @allSelect="selectAllItems"
                                         @changeOrder="updateSorting"
                        />

                        </thead>

                        <tbody v-if="hasRows">

                        <tr v-for="row in rows" :key="row.id">
                            <th class="check-column" scope="row">
                                <input type="checkbox"
                                       class="log-item"
                                       :value="row.id"
                                       v-model="selected"
                                />
                            </th>
                            <td v-for="(value, key) in columns"
                                :class="['column', key]"
                                :key="key"
                            >
                                {{ row[key] }}
                            </td>
                        </tr>

                        </tbody>

                        <tbody v-else>

                        <tr class="no-items">
                            <td class="colspanchange" colspan="6">
                                {{ __( 'No data found.', '404-to-301' ) }}
                            </td>
                        </tr>

                        </tbody>

                        <tfoot>

                        <table-title-row :columns="columns"
                                         @allSelect="selectAllItems"
                                         @changeOrder="updateSorting"
                        />

                        </tfoot>

                    </table>

                    <div class="tablenav bottom">
                        <bulk-action action-key="bulk-actions"
                                     action-label="Bulk Actions"
                                     :action-options="bulkActions"
                                     :is-top="false"
                                     @submit="bulkActionSubmit"
                                     @change="bulkActionChange"
                        />
                        <pagination :total-items="total"
                                    :page="filters.page"
                                    :per-page="filters.per_page"
                                    :is-top="false"
                                    @change="updatePagination"
                        />
                        <br class="clear">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import { restPost, restGet, restDelete } from './../helpers/utils'
	import Pagination from './components/list-table/pagination'
	import BulkAction from './components/list-table/bulk-action'
	import SearchBox from './components/list-table/search-box'
	import TableTitleRow from './components/list-table/table-title-row'

	export default {
		name: 'Logs',

		components: {
			Pagination, BulkAction, TableTitleRow, SearchBox
		},

		created() {
			this.updateRows();
		},

		data() {
			return {
				search: '',
				total: 0,
				selectAll: false,
				selected: [],
				rows: {},
				waiting: false,
				columns: {
					'url': { label: this.__( 'Path', '404-to-301' ), sortable: true },
					'date': { label: this.__( 'Date', '404-to-301' ) },
					'ref': { label: this.__( 'Referral', '404-to-301' ), sortable: true },
					'ip': { label: this.__( 'IP Address', '404-to-301' ), },
					'ua': { label: this.__( 'User Agent', '404-to-301' ), sortable: true },
				},
				bulkActions: {
					trash: this.__( 'Move to Trash', '404-to-301' ),
					delete: this.__( 'Delete All', '404-to-301' ),
				},
				extraActions: [
					{
						key: 'group_by',
						label: this.__( 'Group by', '404-to-301' ),
						options: {
							url: this.__( '404 Path', '404-to-301' ),
							ref: this.__( 'Referral', '404-to-301' ),
							ip: this.__( 'IP Address', '404-to-301' ),
							ua: this.__( 'User Agent', '404-to-301' ),
						},
					},
				],
				filters: {
					order: 'desc',
					order_by: 'id',
					group_by: '',
					page: 1,
					per_page: 2,
					search: '',
				},
			}
		},

		computed: {
			hasRows() {
				return Object.keys( this.rows ).length > 0;
			},

			disableBulkSubmit() {

			}
		},

		methods: {
			/**
			 * Update the settings by sending the value to DB.
			 *
			 * Should handle the error response properly and display
			 * a generic error message.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			updateRows() {
				this.waiting = true;

				restGet( {
					path: 'logs',
					params: this.filters
				} ).then( response => {
					if ( response.success === true ) {
						this.rows = response.data.items;
						this.total = response.data.total;
					} else {
						this.rows = {};
						this.total = 0;
					}

					this.waiting = false;
				} );
			},

			deleteLogs( ids ) {
				this.waiting = true;

				restDelete( {
					path: 'logs',
					data: {
						ids: ids,
					}
				} ).then( response => {
					this.filters.page = 1;
					this.updateRows();
				} );
			},

			updatePagination( data ) {
				this.filters.page = data.page;
				this.updateRows();
			},

			bulkActionSubmit( data ) {
				if ( this.selected.length <= 0 && 'bulk-actions' !== data.action ) {
					return false;
				}

				switch ( data.selected ) {
					case 'trash':
						if ( window.confirm( this.__( 'Are you sure you want to delete all these logs?', '404-to-301' ) ) ) {
							this.deleteLogs( this.selected );
						}
						break;
					case 'delete':
						this.deleteLogs( this.selected );
						break;
				}
			},

			bulkActionChange( data ) {
				if ( 'group_by' !== data.action ) {
					return false;
				}

				this.filters.page = 1;
				this.filters.group_by = data.selected;
				this.updateRows();
			},

			updateSorting( data ) {
				this.filters.page = 1;
				this.filters.order_by = data.key;
				this.filters.order = data.order;
				this.updateRows();
			},

			selectAllItems( data ) {
				let selected = [];
				let rows = this.rows;

				if ( data.checked ) {
					Object.keys( rows ).forEach( function ( key ) {
						selected.push( rows[ key ].id );
					} );
				}

				this.selected = selected;
			},

			searchUrl() {
				this.filters.search = this.search;
				this.updateRows();
			}
		}
	}
</script>
