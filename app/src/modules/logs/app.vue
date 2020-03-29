<template>
    <div id="dd404-admin-logs">
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
                                    {{ this.$i18n.logs.descriptions.no_data }}
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
    </div>
</template>

<script>
	import { restPost, restGet, restDelete } from '@/helpers/api'
	import Pagination from '@/components/list-table/pagination'
	import BulkAction from '@/components/list-table/bulk-action'
	import SearchBox from '@/components/list-table/search-box'
	import TableTitleRow from '@/components/list-table/table-title-row'

	export default {
		name: 'App',

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
					'url': { label: this.$i18n.logs.labels.path, sortable: true },
					'date': { label: this.$i18n.logs.labels.date },
					'ref': { label: this.$i18n.logs.labels.referral, sortable: true },
					'ip': { label: this.$i18n.logs.labels.ip_address, },
					'ua': { label: this.$i18n.logs.labels.user_agent, sortable: true },
				},
				bulkActions: {
					trash: this.$i18n.logs.labels.move_trash,
					delete: this.$i18n.logs.labels.delete_all,
				},
				extraActions: [
					{
						key: 'group_by',
						label: this.$i18n.logs.labels.group_by,
						options: {
							url: this.$i18n.logs.labels.path_404,
							ref: this.$i18n.logs.labels.referral,
							ip: this.$i18n.logs.labels.ip_address,
							ua: this.$i18n.logs.labels.user_agent,
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
						if ( window.confirm( this.$i18n.logs.notices.confirm_delete_all ) ) {
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

<style lang="scss">
    @import "styles/main";
</style>
