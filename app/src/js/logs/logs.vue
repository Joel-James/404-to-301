<template>
    <div id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <div class="tablenav top">
                        <bulk-action v-if="hasBulkActions" action-key="bulk-actions" action-label="Bulk Actions" :action-options="bulkActions" :action-click="false"/>
                        <bulk-action v-if="hasExtraActions" v-for="action in extraActions" :action-label="action.label" :action-options="action.options" :action-key="action.key" :key="action.key"/>
                        <pagination :total-items="totalItems" :page="currentPage" :per-page="perPage"/>
                        <br class="clear">
                    </div>

                    <table class="wp-list-table widefat fixed striped">

                        <thead>
                        <tr>
                            <th class="manage-column column-cb check-column">
                                <input type="checkbox" v-model="selectAll">
                            </th>
                            <td v-for="(value, key) in columns" :class="['column', key]" :key="key" scope="col">
                                {{ value.label }}
                            </td>
                        </tr>
                        </thead>

                        <tbody>
                        <tr v-if="hasRows" v-for="row in rows" :key="row.id">
                            <th class="check-column" scope="row">
                                <input type="checkbox" :value="row.id" v-model="checkedItems">
                            </th>
                            <td v-for="(value, key) in columns" :class="['column', key]" :key="key">
                                {{ row[key] }}
                            </td>
                        </tr>
                        <tr v-else class="no-items">
                            <td class="colspanchange" :colspan="columnCount">{{ labels.emptyRows }}</td>
                        </tr>
                        </tbody>

                    </table>

                    <div class="tablenav bottom">
                        <bulk-action v-if="hasBulkActions" action-key="bulk-actions" action-label="Bulk Actions" :action-options="bulkActions" :is-top="false"/>
                        <pagination :total-items="totalItems" :page="currentPage" :per-page="perPage" :is-top="false"/>
                        <br class="clear">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import { restPost, restGet } from './../helpers/utils'
	import Pagination from './components/list-table/pagination'
	import BulkAction from './components/list-table/bulk-action'

	export default {
		name: 'Logs',

		components: {
			Pagination, BulkAction
		},

		created() {
			this.updateRows();
		},

		mounted() {
			// On bulk action submit.
			this.$root.$on( 'listTableBulkActionSubmit', ( data ) => {
				// Perform action.
			} );

			// On pagination change.
			this.$root.$on( 'listTablePaginationChange', ( data ) => {
				this.updatePagination( data );
			} );
		},

		data() {
			return {
				columns: {
					'path': {
						label: 'Path',
						sortable: true
					},
					'date': {
						label: 'Date'
					},
					'referral': {
						label: 'Referral',
						sortable: true
					},
					'ip': {
						label: 'IP Address'
					},
					'ua': {
						label: 'User Agent',
						sortable: true
					},
				},
				rows: [],
				bulkActions: [
					{
						key: 'trash',
						label: 'Move to Trash'
					}
				],
				extraActions: [
					{
						key: 'group_by',
						label: 'Group by',
						options: [
							{ key: 'path', label: '404 Path' },
							{ key: 'referral', label: 'Referral' },
							{ key: 'ip', label: 'IP' },
							{ key: 'ua', label: 'User Agent' },
						]
					}
				],
				totalItems: 0,
				perPage: 5,
				currentPage: 1,
				labels: {
					emptyRows: 'No data found.',
				},
				selectAll: false,
				checkedItems: [],
			}
		},

		computed: {
			hasRows() {
				return this.rows.length > 0;
			},

			columnCount() {
				let size = Object.keys( this.columns ).length;

				if ( this.showCb ) {
					size = size + 1;
				}

				return size;
			},

			hasBulkActions() {
				return this.bulkActions.length > 0;
			},

			hasExtraActions() {
				return this.extraActions.length > 0;
			}
		},

		methods: {
			/**
			 * Update the settings by sending the value to DB.
			 *
			 * Should handle the error response properly and disply
			 * a generic error message.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			updateRows( page = null ) {
				this.currentPage = page || this.currentPage;

				restGet( {
					path: 'logs',
					params: {
						page: this.currentPage,
						per_page: this.perPage
					}
				} ).then( response => {
					if ( response.success === true ) {
						this.rows = response.data.items;
						this.totalItems = response.data.total;
					} else {
						this.rows = [];
						this.totalItems = 0;
					}

					// End waiting mode.
					this.waiting = false;
				} );
			},

			updatePagination( data ) {
				this.updateRows( data.page );
			}
		}
	}
</script>
