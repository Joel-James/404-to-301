<template>
    <form id="general-settings" @submit="submitForm" method="post">

        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="redirectType">{{ labels.redirectType }}</label></th>
                <td>
                    <select id="redirectType" v-model="redirectType">
                        <option value="301">301 Redirect (SEO)</option>
                        <option value="302">302 Redirect</option>
                        <option value="307">307 Redirect</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="redirectTo">{{ labels.redirectTo }}</label></th>
                <td>
                    <select id="redirectTo" v-model="redirectTo">
                        <option value="page">Existing Page</option>
                        <option value="link">Custom URL</option>
                        <option value="none">No Redirect</option>
                    </select>
                    <p class="description" v-if="'page' === redirectTo">Select any WordPress page as a 404 page.</p>
                    <p class="description" v-if="'link' === redirectTo">Redirect 404 requests to a specific URL.</p>
                    <p class="description" v-if="'none' === redirectTo">To disable redirect.</p>
                    <p class="description"><strong>You can override this by setting individual custom redirects from error logs list.</strong></p>
                </td>
            </tr>
            <tr v-if="'page' === redirectTo">
                <th><label for="redirectPage">{{ labels.redirectPage }}</label></th>
                <td>
                    <select id="redirectPage" v-model="redirectPage">
                        <option value="page">Existing Page</option>
                        <option value="link">Custom URL</option>
                        <option value="none">No Redirect</option>
                    </select>
                </td>
            </tr>
            <tr v-if="'link' === redirectTo">
                <th><label for="redirectLink">{{ labels.redirectLink }}</label></th>
                <td>
                    <input type="url" id="redirectLink" v-model="redirectLink">
                </td>
            </tr>
            <tr>
                <th><label for="redirectLog">{{ labels.redirectLog }}</label></th>
                <td>
                    <input type="checkbox" id="redirectLog" v-model="redirectLog">
                </td>
            </tr>
            <tr>
                <th><label for="disableGuessing">{{ labels.disableGuessing }}</label></th>
                <td>
                    <input type="checkbox" id="disableGuessing" v-model="disableGuessing">
                </td>
            </tr>
            <tr>
                <th><label for="excludePaths">{{ labels.excludePaths }}</label></th>
                <td>
                    <textarea id="excludePaths" v-model="excludePaths"></textarea>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input
                            type="submit"
                            name="submit"
                            class="button button-primary"
                            value="Save Changes"
                            v-bind:disabled="waiting"
                    >
                </th>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
	import { __ } from '@wordpress/i18n';
	import { restPost } from '../../../helpers/utils';

	export default {

		/**
		 * Current template name.
		 *
		 * @since 4.0.0
		 */
		name: 'General',

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		data() {

			return {
				redirectType: dd404.settings.general.redirect_type,
				redirectTo: dd404.settings.general.redirect_to,
				redirectPage: dd404.settings.general.redirect_page,
				redirectLink: dd404.settings.general.redirect_link,
				redirectLog: dd404.settings.general.redirect_log,
				disableGuessing: dd404.settings.general.disable_guessing,
				excludePaths: dd404.settings.general.exclude_paths,
				waiting: false,
				labels: {
					redirectType: __( 'Redirect type', '404-to-301' ),
					redirectTo: __( 'Redirect to', '404-to-301' ),
					redirectPage: __( 'Select the page', '404-to-301' ),
					redirectLink: __( 'Custom URL', '404-to-301' ),
					redirectLog: __( 'Log 404 Errors', '404-to-301' ),
					disableGuessing: __( 'Disable URL guessing', '404-to-301' ),
					excludePaths: __( 'Exclude paths', '404-to-301' )
				}
			}
		},

		methods: {
			/**
			 * Handle settings for submit.
			 *
			 * Validate the form before submitting it.
			 *
			 * @param e Event.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			submitForm: function ( e ) {
				// Start waiting mode.
				this.waiting = true;

				this.updateSettings();

				// Do not submit form.
				e.preventDefault();
			},

			/**
			 * Update the settings by sending the value to DB.
			 *
			 * Should handle the error response properly and disply
			 * a generic error message.
			 *
			 * @since 4.0.0
			 *
			 * @returns {boolean}
			 */
			updateSettings: function () {
				restPost( {
					path: 'settings',
					data: {
						group: 'general',
						value: {
							redirect_type: this.redirectType,
							redirect_to: this.redirectTo,
							redirect_page: this.redirectPage,
							redirect_link: this.redirectLink,
							redirect_log: this.redirectLog,
							disable_guessing: this.disableGuessing,
							exclude_paths: this.excludePaths,
						}
					}
				} ).then( response => {
					if ( response.success === true ) {
						// Show success message.
						this.$parent.showNotice();

						// Update settings in DOM.
						this.$parent.updateSettings( response.data, 'general' );
					} else {
						// Show error message.
						this.$parent.showNotice( false );
					}

					// End waiting mode.
					this.waiting = false;
				} );
			},
		}
	}
</script>
