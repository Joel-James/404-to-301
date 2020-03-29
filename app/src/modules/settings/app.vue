<template>
    <div id="dd404-admin-settings">

        <div v-if="alert" class="notice is-dismissible" v-bind:class="noticeClass">
            <p>{{ alert }}</p>
        </div>

        <tabs selected="general">
            <tab key="general"
                 :title="$i18n.settings.titles.general"
                 icon="admin-generic"
                 :selected="true"
            >
                <general/>
            </tab>
            <tab key="email"
                 :title="$i18n.settings.titles.email"
                 icon="email"
            >
                <email/>
            </tab>
        </tabs>

    </div>
</template>

<script>
	import Email from './tabs/email'
	import General from './tabs/general'
	import Tab from '@/components/settings-tabs/tab'
	import Tabs from '@/components/settings-tabs/tabs'

	export default {

		/**
		 * Current template name.
		 *
		 * @since 4.0.0
		 */
		name: 'App',

		components: { Tab, Tabs, General, Email },

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		data() {
			return {
				alert: false,
				alertType: 'success',
				tab: 'general',
			}
		},

		computed: {
			/**
			 * Get the notice class based on the alert type.
			 *
			 * We use inbuilt WP admin notice classes.
			 *
			 * @since 4.0.0
			 *
			 * @returns {string}
			 */
			noticeClass() {
				return {
					'notice-success': this.alertType === 'success',
					'notice-error': this.alertType === 'error',
					'notice-warning': this.alertType === 'warning',
					'notice-info': this.alertType === 'info',
				}
			}
		},

		methods: {
			/**
			 * Show an notice message on top of the page.
			 *
			 * Alter messages uses WP's admin notice classes.
			 *
			 * @param {boolean} success Alert type success or error.
			 * @param {boolean} autoHide Should hide automatically.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			showNotice( success = true, autoHide = true ) {
				this.alertType = success ? 'success' : 'error';

				// Set meesage.
				if ( success ) {
					this.alert = this.$i18n.settings.notices.settings_updated;
				} else {
					this.alert = this.$i18n.settings.notices.settings_update_failed;
				}

				// Auto hide if required.
				if ( autoHide ) {
					setTimeout( () => {
						this.alert = false;
						this.alertType = 'success';
					}, 3000 );
				}
			},

			/**
			 * Update the settings section in DOM.
			 *
			 * Once we update the settings in db, router will still
			 * access to old data from DOM. So update DOM also.
			 *
			 * @param {object} settings New settings data.
			 * @param {string} group Settings group.
			 *
			 * @since 4.0.0
			 *
			 * @returns {void}
			 */
			updateSettings( settings, group ) {
				this.$vars.settings[ group ] = settings;
			},
		}
	}
</script>

<style lang="scss">
    @import "styles/main";
</style>
