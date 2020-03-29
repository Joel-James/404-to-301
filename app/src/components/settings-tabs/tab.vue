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
            },
            title: {
				type: String,
                required: true,
            },
			id: {
				type: String,
				required: true,
			},
			icon: {
				type: String,
				default: '',
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
