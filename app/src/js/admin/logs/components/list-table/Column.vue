<template>
    <th v-if="isHead" :class="columnClass" v-bind:scope="scope">
        <slot></slot>
    </th>
    <td v-else :class="columnClass" v-bind:scope="scope">
        <slot></slot>
    </td>
</template>

<script>
	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'Column',

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			isHead: {
				type: Boolean,
				default: false,
			},
			columnClass: {
				type: Array,
				default: [ 'column' ],
			},
			scope: {
				type: String,
				default: null
			}
		},
	};
</script>