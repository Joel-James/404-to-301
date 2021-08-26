<template>
	<table class="duckdev-table">
		<tbody>
		<tr v-for="(item, index) in rows" :key="item">
			<td>
				<input
					type="text"
					class="large-text"
					name="404_to_301_settings[exclude_paths][]"
					v-model="item.value"
					placeholder="wp-content/"
				>
			</td>
			<td>
				<button
					type="button"
					class="button duckdev-inline-button"
					@click="deleteRow(index)"
				>
					<span class="dashicons dashicons-trash"></span>
				</button>
			</td>
		</tr>
		</tbody>
		<tfoot>
		<tr>
			<td colspan="2">
				<button
					type="button"
					class="button button-primarys button-small duckdev-inline-button"
					@click="addRow"
				>
					<span class="dashicons dashicons-plus"></span>
					{{ __('Add', '404-to-301') }}
				</button>
			</td>
		</tr>
		</tfoot>
	</table>
</template>

<script>
export default {
	name: 'RepeatTable',

	data() {
		let rows = [],
			items = window?.dd4t3?.settings?.exclude_paths ?? []
		// Setup rows from settings.
		items.forEach((item) => {
			rows.push({value: item})
		})

		return {
			rows: rows,
		}
	},

	methods: {
		/**
		 * Add new row to the table.
		 *
		 * @since 4.0.0
		 */
		addRow() {
			// Push new row.
			this.rows.push({value: ''})
		},

		/**
		 * Delete one row using it's index.
		 *
		 * @param {int} index Index of item.
		 *
		 * @since 4.0.0
		 */
		deleteRow(index) {
			// Remove one item from array.
			this.rows.splice(index, 1);
		}
	}
}
</script>
