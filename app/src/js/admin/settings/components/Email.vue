<template>
    <form id="email-settings" @submit="submitForm" method="post">

        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="emailNotify">{{ labels.emailNotify }}</label></th>
                <td>
                    <input type="checkbox" id="emailNotify" v-model="emailNotify" value="1">
                </td>
            </tr>
            <tr>
                <th><label for="emailRecipient">{{ labels.emailRecipient }}</label></th>
                <td>
                    <input type="email" id="emailRecipient" v-model="emailRecipient">
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="submit" name="submit" class="button button-primary" value="Save Changes"
                           v-bind:disabled="waiting">
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

		name: 'Email',

		data() {
			return {
				emailNotify: 0,
				emailRecipient: null,
				waiting: false,
				labels: {
					emailNotify: __( 'Email notifications', '404-to-301' ),
					emailRecipient: __( 'Email address', '404-to-301' ),
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
						group: 'email',
						value: {
							email_notify: this.emailNotify,
							email_notify_address: this.emailRecipient,
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
					path: 'settings/email/'
				} ).then( response => {
					this.emailNotify = response.data.email_notify;
					this.emailRecipient = response.data.email_notify_address;
				} );
			},

			showSuccess: function () {
				this.$parent.showAlert( __( 'Email settings updated successfully.', '404-to-301' ) );
			},
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
