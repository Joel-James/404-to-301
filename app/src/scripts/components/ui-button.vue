<template>
	<button
		type="button"
		class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
	>
		{{ label }}
	</button>
</template>

<script>
export default {
	name: 'UiButton',

	components: {},

	props: {
		label: String,
		color: {
			type: String,
			default: 'emerald-400',
		},
		colorHover: {
			type: String,
			default: 'emerald-600',
		},
	},

	computed: {
		btnClass() {
			return {}
		},
	},
}
</script>
