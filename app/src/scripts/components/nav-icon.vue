<template>
	<svg
		class="flex-shrink-0 -ml-1 mr-3 h-6 w-6"
		xmlns="http://www.w3.org/2000/svg"
		fill="none"
		viewBox="0 0 24 24"
		stroke="currentColor"
		aria-hidden="true"
		:class="iconClass"
	>
		<component :is="icon"></component>
	</svg>
</template>

<script>
import Cog from './svg-icons/navs/cog'
import Mail from './svg-icons/navs/mail'
import Reply from './svg-icons/navs/reply'
import ClipboardList from './svg-icons/navs/clipboard-list'

export default {
	name: 'NavIcon',

	components: {
		Cog,
		Mail,
		Reply,
		ClipboardList,
	},

	props: {
		icon: {
			type: String,
			required: true,
		},
		active: {
			type: Boolean,
			default: false,
		},
	},

	computed: {
		/**
		 * Get the icon class for active inactive link.
		 *
		 * @since 4.0.0
		 *
		 * @return {*}
		 */
		iconClass() {
			return {
				'text-teal-500': this.active,
				'group-hover:text-teal-500': this.active,
				'text-gray-400 ': !this.active,
				'group-hover:text-gray-500': !this.active,
			}
		},
	},
}
</script>
