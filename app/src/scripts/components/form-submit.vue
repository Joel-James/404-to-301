<template>
		<button
			type="submit"
			class="button button-primary"
			:disabled="disable"
			@click="saveSettings"
		>
			{{ saving ? savingText : saveText }}
		</button>
		<button
			type="reset"
			class="button"
			:disabled="disable"
			@click="resetSettings"
		>
			{{ resetting ? resettingText : resetText }}
		</button>
</template>

<script>
export default {
	name: 'FormSubmit',

	props: {
		saveText: String,
		savingText: String,
		resetText: String,
		resettingText: String,
	},

	data() {
		return {
			saving: false,
			resetting: false
		}
	},

	computed: {
		/**
		 * Check if button needs to be disabled.
		 *
		 * @since 4.0
		 *
		 * @return {boolean}
		 */
		disable() {
			return this.saving || this.resetting
		}
	},

	methods: {
		/**
		 * Save settings using API.
		 *
		 * Make the button animation white saving.
		 *
		 * @since 4.0
		 */
		saveSettings() {
			this.saving = true
		},

		/**
		 * Reset settings using API.
		 *
		 * Make the button animation white saving.
		 *
		 * @since 4.0
		 */
		resetSettings() {
			this.resetting = true
		},
	}
}
</script>
