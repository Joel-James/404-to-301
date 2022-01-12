<template>
	<a
		href="#"
		:class="tabClass"
		@click="tabClick"
	>
			<span
				v-if="icon"
				:class="`dashicons dashicons-${icon} duckdev-dashicons-tabs`"
			></span>
		{{ label }}
	</a>
</template>

<script>
export default {
	name: 'TabItem',

	props: {
		icon: String,
		label: String,
		name: String,
		active: String,
	},

	computed: {
		/**
		 * Get class names for current tab.
		 *
		 * @since 4.0.0
		 *
		 * @return {{"nav-tab-active": boolean, "nav-tab": boolean}}
		 */
		tabClass() {
			return {
				'nav-tab': true,
				'nav-tab-active': this.active === this.name
			}
		}
	},

	methods: {
		/**
		 * Process tab item click event.
		 *
		 * @since 4.0.0
		 */
		tabClick() {
			this.$emit('tabClick', this.name)
		}
	}
}
</script>
