<template>
	<table class="tsf-table w-full">
		<tbody>
		<tr v-for="(item, index) in rows" :key="item">
			<td>
				<input
					type="text"
					class="large-text"
					name="404_to_301_settings[general][exclude][]"
					v-model="item.value"
					placeholder="wp-content/"
				>
			</td>
			<td>
				<button
					type="button"
					class="button inline-flex items-center"
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
					class="tsf-set-image-button button button-primarys button-small inline-flex items-center"
					@click="addRow"
				>
					<span class="dashicons dashicons-plus"></span>
					Add
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
		let rows = [];
		window.dd404.settings.general.exclude.forEach((item) => {
			rows.push({value:item})
		})
		return {
			rows: rows,
		}
	},

	methods: {
		addRow() {
			this.rows.push({value: ''})
		},

		deleteRow(index) {
			this.rows.splice(index, 1);
		}
	}
}
</script>
