<template>
    <thead>
    <tr>
        <Column v-if="showCb" :columnClass="['manage-column', 'column-cb', 'check-column']">
            <input type="checkbox" v-model="selectAll">
        </Column>
        <Column v-for="(value, key) in columns" :columnClass="['column', key]">
            {{ value.label }}
        </Column>
    </tr>
    </thead>
</template>

<script>
	import Column from './Column.vue'

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Header',

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
			columns: {
				type: Object,
				required: true,
				default: {},
			},
			showCb: {
				type: Boolean,
				default: true,
			},
		},

		/**
		 * Dynamic methods to handle table.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		computed: {
			/**
			 * Select and unselect all child items.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			selectAll: {
				get: function () {},
				set: function () {}
			}
		}
	};
</script>