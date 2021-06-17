import Vue from 'vue'

Vue.component('form-submit', {
	template: '#form-submit-template',

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
		saveSettings() {
			this.processing = true
		}
	}
})
