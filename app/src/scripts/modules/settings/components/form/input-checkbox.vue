<template>
	<label :for="id">
		<input type="hidden" :name="name" :value="enabled ? 1 : 0">
		<input
			type="checkbox"
			:id="id"
			v-model="enabled"
		> {{ label }}
	</label>
</template>

<script>
export default {
	name: 'InputCheckbox',

	props: {
		id: String,
		name: String,
		label: String,
		checked: Number,
	},

	data() {
		return {
			enabled: this.checked
		}
	},
}
</script>
