<template>
	<!-- Enabled: "bg-teal-500", Not Enabled: "bg-gray-200" -->
	<button
		type="button"
		class="ml-4 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wpblue-700"
		aria-pressed="true"
		aria-labelledby="privacy-option-1-label"
		aria-describedby="privacy-option-1-description"
		:class="buttonClass"
		@click="handleToggle"
	>
		<span class="sr-only">{{ label }}</span>
		<!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
		<span
			aria-hidden="true"
			class="inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
			:class="spanClass"
		></span>
	</button>
</template>

<script>
export default {
	name: 'UiToggle',

	components: {},

	props: {
		label: String,
		value: {
			type: Boolean,
			default: false,
		},
	},

	data() {
		return {
			enabled: this.value,
		}
	},

	computed: {
		buttonClass() {
			return {
				'bg-wpblue-700': this.enabled,
				'bg-gray-200': !this.enabled,
			}
		},

		spanClass() {
			return {
				'translate-x-5': this.enabled,
				'translate-x-0': !this.enabled,
			}
		},
	},

	methods: {
		/**
		 * Handle change event of select element.
		 *
		 * @since 3.2.0
		 */
		handleToggle() {
			// Update model.
			this.enabled = !this.enabled
			// Emit input event.
			this.$emit('input', this.enabled)
		},
	},
}
</script>
