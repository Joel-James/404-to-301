<template>

    <div v-if="isActive">
        <slot></slot>
    </div>

</template>

<script>
	export default {
		name: 'Tab',

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		props: {
			selected: {
				type: Boolean,
                default: false
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
				isActive: false
			}
		},

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		mounted() {
			this.isActive = this.selected
		},
	}
</script>
