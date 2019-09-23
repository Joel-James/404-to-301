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
	import { restPost, restGet } from '../../../helpers/utils';

	export default {

		name: 'General',

		data() {

			return {
				alert: false,
				redirectType: '301',
				redirectTo: null,
				redirectPage: null,
				redirectLink: null,
				redirectLog: 1,
				disableGuessing: 1,
				excludePaths: '',
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

		created() {
			// Retrieve the selected form.
			this.getSettings();
		},

		methods: {
			/**
			 * Handle settings for submit.
			 *
			 * Validate the form before submitting it.
			 *
			 * @param e Event.
			 *
			 * @returns {boolean}
			 */
			submitForm: function ( e ) {
				this.waiting = true;

				this.updateSettings();

				// Do not submit form.
				e.preventDefault();
			},

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
					this.showSuccess();
					this.waiting = false;
				} );
				this.showSuccess();
			},

			getSettings: function () {
				restGet( {
					path: 'settings/general/'
				} ).then( response => {
					this.redirectType = response.data.redirect_type;
					this.redirectTo = response.data.redirect_to;
					this.redirectPage = response.data.redirect_page;
					this.redirectLink = response.data.redirect_link;
					this.redirectLog = response.data.redirect_log;
					this.disableGuessing = response.data.disable_guessing;
					this.excludePaths = response.data.exclude_paths;
				} );
			},

			showSuccess: function () {
				this.$parent.showAlert( __( 'General settings updated successfully.', '404-to-301' ) );
			},
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
