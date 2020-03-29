<template>

    <div>
        <nav class="nav-tab-wrapper">
            <a v-for="tab in tabs"
               :key="tab.id"
               class="nav-tab"
               :class="{ 'nav-tab-active' : tab.isActive }"
               @click="selectTab(tab.id)"
            >
                <span v-if="tab.icon" :class="`dashicons dashicons-${tab.icon}`"></span>
                {{ tab.title }}
            </a>
        </nav>
        <slot></slot>
    </div>

</template>

<script>
	export default {
		name: 'Tabs',

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		data() {
			return {
				tabs: [],
			}
		},

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		created() {
			this.tabs = this.$children
		},

		methods: {
			/**
			 * Show an notice message on top of the page.
			 *
			 * Alter messages uses WP's admin notice classes.
			 *
			 * @param {string} selected Tab key to select.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			selectTab( selected ) {
				this.tabs.forEach( tab => {
					tab.isActive = ( tab.id === selected );
				} );
			}
		}
	}
</script>
