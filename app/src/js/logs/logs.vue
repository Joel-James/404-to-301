<template>
    <div id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="tablenav top">
                        <bulk-action action-key="bulk-actions" action-label="Bulk Actions" :action-options="bulkActions"/>
                        <bulk-action v-for="action in extraActions" :action-label="action.label" :action-options="action.options" :action-key="action.key" :enable-submit="false" :key="action.key"/>
                        <span v-if="waiting" class="table-loader">
                            <span class="spinner is-active"></span>
                        </span>
                        <pagination :total-items="totalItems" :page="filterOptions.page" :per-page="filterOptions.per_page"/>
                        <br class="clear">
                    </div>

                    <table class="wp-list-table widefat fixed striped">

                        <thead>
                        <table-title-row :columns="columns"></table-title-row>
                        </thead>

                        <tbody v-if="hasRows">
                        <tr v-for="row in rows" :key="row.id">
                            <th class="check-column" scope="row">
                                <input type="checkbox" class="log-item" :value="row.id" v-model="checkedItems">
                            </th>
                            <td v-for="(value, key) in columns" :class="['column', key]" :key="key">
                                {{ row[key] }}
                            </td>
                        </tr>
                        </tbody>
                        <tbody v-else>
                        <tr class="no-items">
                            <td class="colspanchange" colspan="6">{{ __( 'No data found.', '404-to-301' ) }}</td>
                        </tr>
                        </tbody>

                        <tfoot>
                        <table-title-row :columns="columns"></table-title-row>
                        </tfoot>

                    </table>

                    <div class="tablenav bottom">
                        <bulk-action action-key="bulk-actions" action-label="Bulk Actions" :action-options="bulkActions" :is-top="false"/>
                        <pagination :total-items="totalItems" :page="filterOptions.page" :per-page="filterOptions.per_page" :is-top="false"/>
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
	import TableTitleRow from './components/list-table/table-title-row'

	export default {
		name: 'Logs',

		components: {
			Pagination, BulkAction, TableTitleRow
		},

		created() {
			this.updateRows();
		},

		mounted() {
			// On bulk action submit.
			this.$root.$on( 'listTableBulkActionSubmit', ( data ) => {
				this.processBulkActionSubmit( data );
			} );

			// On bulk action change.
			this.$root.$on( 'listTableBulkActionChange', ( data ) => {
				this.processBulkActionChange( data );
			} );

			// On pagination change.
			this.$root.$on( 'listTablePaginationChange', ( data ) => {
				this.updatePagination( data );
			} );

			// On sorting change.
			this.$root.$on( 'listTableSortOrderChanged', ( data ) => {
				this.updateSorting( data );
			} );

			// On sorting change.
			this.$root.$on( 'listTableAllSelected', ( data ) => {
				this.selectAllItems( data );
			} );
		},

		data() {
			return {
				totalItems: 0,
				selectAll: false,
				checkedItems: [],
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
							path: this.__( '404 Path', '404-to-301' ),
							referral: this.__( 'Referral', '404-to-301' ),
							ip: this.__( 'IP', '404-to-301' ),
							ua: this.__( 'User Agent', '404-to-301' ),
						},
					},
				],
				filterOptions: {
					sort_order: 'desc',
					sort_by: 'id',
					group_by: '',
					page: 1,
					per_page: 5,
				}
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
					params: this.filterOptions
				} ).then( response => {
					if ( response.success === true ) {
						this.rows = response.data.items;
						this.totalItems = response.data.total;
					} else {
						this.rows = {};
						this.totalItems = 0;
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
					this.filterOptions.page = 1;
					this.updateRows();
				} );
			},

			updatePagination( data ) {
				this.filterOptions.page = data.page;
				this.updateRows();
			},

			processBulkActionSubmit( data ) {
				if ( this.checkedItems.length <= 0 && 'bulk-actions' !== data.action ) {
					return false;
                }

				switch ( data.selected ) {
                    case 'trash':
                    	if ( window.confirm( this.__( 'Are you sure you want to delete all these logs?', '404-to-301' ) ) ) {
							this.deleteLogs( this.checkedItems );
                        }
						break;
					case 'delete':
						this.deleteLogs( this.checkedItems );
						break;
				}
			},

			processBulkActionChange( data ) {
				if ( 'group_by' !== data.action ) {
					return false;
				}

				this.filterOptions.group_by = data.group;
				this.updateRows();
			},

			updateSorting( data ) {
				this.filterOptions.page = 1;
				this.filterOptions.sort_by = data.key;
				this.filterOptions.sort_order = data.order;
				this.updateRows();
			},

			selectAllItems( data ) {
				let checkedItems = [];
				let rows = this.rows;

				if ( data.checked ) {
					Object.keys( rows ).forEach( function ( key ) {
						checkedItems.push( rows[ key ].id );
					} );
				}

				this.checkedItems = checkedItems;
			}
		}
	}
</script>
