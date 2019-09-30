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
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
	import Table from './list-table/Table.vue'

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
				rows: [
					{
						id: 1,
						path: 'test',
						date: '20/12/2091',
						referral: 'none',
						ip: '127.0.0.1',
						ua: 'none'
					},
					{
						id: 2,
						path: 'test',
						date: '20/12/2091',
						referral: 'none',
						ip: '127.0.0.1',
						ua: 'none'
					}
				],
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
				]
			}
		},

        methods: {
			updateRows( page ) {
				window.console.log('page : ' + page);
            }
        }
	}
</script>
