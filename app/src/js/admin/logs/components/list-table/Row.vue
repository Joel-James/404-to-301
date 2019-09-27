<template>
    <tr>
        <Column v-if="showCb" :column-class="['check-column']" :is-head="true" :scope="'row'">
            <input type="checkbox" name="item[]" :value="id" v-model="checkedItems">
        </Column>
        <Column v-for="(value, key) in columns" :column-class="['column', key]" :is-head="false">
            {{ row[key] }}
        </Column>
    </tr>
</template>

<script>
	import Column from './Column.vue'

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Row',

		/**
		 * Required components in this component.
		 *
		 * @since 4.0.0
		 */
		components: {
			Column
		},

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			id: {
				type: Number,
				required: true,
			},
			columns: {
				type: Object,
				required: true,
				default: {},
			},
			row: {
				type: Object,
				required: true,
				default: {},
			},
			showCb: {
				type: Boolean,
				default: true,
			}
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
				checkedItems: [],
			};
		}
	};
</script>