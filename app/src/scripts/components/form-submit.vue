<template>
	<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
		<button
			type="button"
			class="bg-gray-800 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-gray-900"
			@click="saveSettings"
		>
			<svg v-if="processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
				<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
				<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
			</svg>
			{{ buttonText }}
		</button>
	</div>
</template>

<script>
export default {
	name: 'FormSubmitButton',

	props: {
		saveText: String,
		savingText: String,
	},

	data() {
		return {
			processing: false
		}
	},

	computed: {
		/**
		 * Submit button text based on processing status.
		 *
		 * @since 4.0
		 *
		 * @return {String}
		 */
		buttonText() {
			return this.processing ? this.savingText : this.saveText
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
		async saveSettings() {
			this.processing = true

			await this.$axios({
				url: 'settings'
			})
			.then(function (response) {
				console.log(response);
			})
			.catch(function (error) {
				console.log(error);
			})
			.then(function () {
				// always executed
			});

			this.processing = false
		},
	}
}
</script>
