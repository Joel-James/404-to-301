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

	export default {

		name: 'Email',

		data() {
			return {
				emailNotify: 0,
				emailRecipient: 'joel@joel.com',
				waiting: false,
				labels: {
					emailNotify: __( 'Email notifications', '404-to-301' ),
					emailRecipient: __( 'Email address', '404-to-301' ),
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

				// Do not submit form.
				e.preventDefault();
			},

			showSuccess: function () {
				this.$parent.alert = __( 'Thanks for the message, Joel.' );

				setTimeout( () => {
					this.$parent.alert = false;
					this.waiting = false;
				}, 3000 );
			}
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
