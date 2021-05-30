<template>
	<a
		class="group border-l-4 px-3 py-2 flex items-center text-sm font-medium"
		:class="linkClasses"
		@click="changeRoute"
	>
		<nav-icon :icon="icon" :active="isActive" v-if="showIcon" />
		<span class="truncate">
			{{ label }}
		</span>
	</a>
</template>

<script>
import NavIcon from './nav-icon'

export default {
	name: 'UiRouteLink',

	components: { NavIcon },

	props: {
		path: {
			type: String,
			required: true,
		},
		label: {
			type: String,
			required: true,
		},
		icon: {
			type: String,
			default: 'cog',
		},
	},

	computed: {
		/**
		 * Check if current route is active.
		 *
		 * @since 4.0.0
		 *
		 * @return {boolean}
		 */
		isActive() {
			return this.$route.path === this.path
		},

		/**
		 * Check if we can show icon.
		 *
		 * @since 4.0.0
		 *
		 * @return {boolean}
		 */
		showIcon() {
			return this.icon !== ''
		},

		/**
		 * Get the link class based on the active status.
		 *
		 * @since 4.0.0
		 *
		 * @return {*}
		 */
		linkClasses() {
			return {
				'bg-teal-50': this.isActive,
				'border-teal-500': this.isActive,
				'text-teal-700': this.isActive,
				'hover:bg-teal-50': this.isActive,
				'hover:text-teal-700': this.isActive,
				'border-transparent': !this.isActive,
				'text-gray-900': !this.isActive,
				'hover:bg-gray-50': !this.isActive,
				'hover:text-gray-900': !this.isActive,
			}
		},
	},

	methods: {
		/**
		 * Change the current active route.
		 *
		 * Do not change if the new route is same
		 * as currently active route.
		 *
		 * @since 4.0.0
		 *
		 * @return {void}
		 */
		changeRoute() {
			if (!this.isActive) {
				this.$router.push({ path: this.path })
			}
		},
	},
}
</script>
