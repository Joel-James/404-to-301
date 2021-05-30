<template>
	<button
		type="button"
		class="ml-5 bg-wpblue-700 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-wpblue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wpblue-600"
		:disabled="saving"
		@click="processSave"
	>
		{{ buttonText }}
	</button>
</template>

<script>
export default {
	name: 'UiSaveButton',

	components: {},

	props: {},

	data() {
		return {
			saving: false,
		}
	},

	computed: {
		buttonText() {
			return this.saving ? 'Saving..' : 'Save'
		},
	},

	methods: {
		processSave() {
			this.saving = true

			setTimeout(() => (this.saving = false), 2000)
		},
	},
}
</script>
