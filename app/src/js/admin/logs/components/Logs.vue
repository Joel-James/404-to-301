<template>
    <div id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <Table
                            :columns="columns"
                            :rows="rows"
                            :bulk-actions="bulkActions"
                            :extra-actions="extraActions"
                            :pagination-callback="updateRows"
                            :total-items="totalItems"
                            :per-page="perPage"
                            :current-page="currentPage"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import Table from './list-table/Table.vue'
	import { restPost, restGet } from '../../../helpers/utils'

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Logs',

		/**
		 * Required components in this component.
		 *
		 * @since 4.0.0
		 */
		components: {
			Table
		},

		created() {
			this.updateRows();
		},

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
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
				totalItems: 25,
				perPage: 4,
				currentPage: 1
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
						this.rows = response.data;
						this.totalItems = response.data.length;
					} else {
						this.rows = [];
						this.totalItems = 0;
					}

					// End waiting mode.
					this.waiting = false;
				} );
			},
		}
	}
</script>
