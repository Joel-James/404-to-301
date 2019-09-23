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
		name: 'SettingsApp',
		data() {
			return {
				alert: false,
				alertType: 'success',
				labels: {
					general: __( 'General', '404-to-301' ),
					email: __( 'Email', '404-to-301' ),
					logs: __( 'Logs', '404-to-301' ),
				}
			}
		},

		computed: {
			noticeClass: function () {
				return {
					'notice-success' : this.alertType === 'success',
					'notice-error' : this.alertType === 'error',
					'notice-warning' : this.alertType === 'warning',
					'notice-info' : this.alertType === 'info',
				}
			}
		},

		methods: {
			/**
			 * Show an alter message on top of the page.
			 *
			 * Alter messages uses WP's admin notice classes.
			 *
			 * @param {string} message Altert content.
			 * @param {string} alertType Alert type (error, success, warning, info).
			 * @param {boolean} autoHide Should hide automatically.
             *
             * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			showAlert: function ( message, alertType = 'success', autoHide = true ) {
				this.alert = message;
				this.alertType = alertType;

				if ( autoHide ) {
					setTimeout( () => {
						this.alert = false;
						this.alertType = 'success';
					}, 3000 );
				}
			}
		}
	}
</script>

<style>

</style>
