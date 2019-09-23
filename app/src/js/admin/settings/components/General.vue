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
                    <span
                            class="spinner"
                            v-bind:class="{ 'is-active' : waiting }"
                    >
                    </span>
                </th>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
	import { __ } from '@wordpress/i18n';
	import { restRequest } from '../../../helpers/utils';

	export default {

		name: 'General',

		data() {
			return {
				alert: false,
				redirectType: '301',
				redirectTo: 'link',
				redirectPage: 'link',
				redirectLink: 'http://google.com',
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
				this.showSuccess();
				this.waiting = true;

				restRequest( { path: 'settings' } ).then( response => {
					console.log( response );
				} );

				// Do not submit form.
				e.preventDefault();
			},

			showSuccess: function () {
				this.$parent.showAlert( __( 'Thanks for the message, Joel.', '404-to-301' ), 'info' );
			},
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
