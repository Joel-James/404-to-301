<template>
	<li>
		<router-link
			:to="name"
		>
			<span
				v-if="icon"
				:class="`dashicons dashicons-${icon} duckdev-dashicons-tabs`"
			></span>
			{{ label }}
		</router-link>
	</li>
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
		 * @return {*}
		 */
		tabClass() {
			return {
				'current': this.active === this.name
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
