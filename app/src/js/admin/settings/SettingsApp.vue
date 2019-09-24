<template>
    <div id="dd404-admin-settings">

        <div v-if="alert" class="notice is-dismissible" v-bind:class="noticeClass">
            <p>{{ alert }}</p>
        </div>

        <nav class="nav-tab-wrapper">
            <router-link to="/" class="nav-tab" exact>
                <span class="dashicons dashicons-admin-generic"></span>
                {{ labels.general }}
            </router-link>

            <router-link to="/email" class="nav-tab">
                <span class="dashicons dashicons-email"></span>
                {{ labels.email }}
            </router-link>
        </nav>

        <router-view/>

    </div>
</template>

<script>
	import { __ } from '@wordpress/i18n';

	export default {

		/**
		 * Current template name.
		 *
		 * @since 4.0.0
		 */
		name: 'SettingsApp',

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
				labels: {
					general: __( 'General', '404-to-301' ),
					email: __( 'Email', '404-to-301' ),
				}
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
			noticeClass: function () {
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
			showNotice: function ( success = true, autoHide = true ) {
				this.alertType = success ? 'success' : 'error';

				// Set meesage.
				if ( success ) {
					this.alert = __( 'Settings updated successfully.', '404-to-301' );
				} else {
					this.alert = __( 'Oops! Something went wrong.', '404-to-301' );
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
			updateSettings: function ( settings, group ) {
				window.dd404.settings[ group ] = settings;
			}
		}
	}
</script>
