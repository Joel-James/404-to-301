<template>
    <form id="email-settings" @submit="submitForm" method="post">

        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="emailNotify">{{ $i18n.settings.labels.email_notification }}</label></th>
                <td>
                    <input type="checkbox"
                           id="emailNotify"
                           v-model="emailNotify"
                    >
                </td>
            </tr>
            <tr>
                <th><label for="emailRecipient">{{ $i18n.settings.labels.email_address }}</label></th>
                <td>
                    <input type="email"
                           id="emailRecipient"
                           v-model="emailRecipient"
                           :disabled="!emailNotify"
                    >
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input
                            type="submit"
                            name="submit"
                            class="button button-primary"
                            :value="$i18n.buttons.save_changes"
                            :disabled="waiting"
                    >
                </th>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
	import { restPost } from '@/helpers/api';

	export default {

		/**
		 * Current template name.
		 *
		 * @since 4.0.0
		 */
		name: 'Email',

		/**
		 * Get the default set of data for the template.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		data() {
			return {
				emailNotify: dd404.settings.email.email_notify,
				emailRecipient: dd404.settings.email.email_notify_address,
				waiting: false,
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
						group: 'email',
						value: {
							email_notify: this.emailNotify,
							email_notify_address: this.emailRecipient,
						}
					}
				} ).then( response => {
					if ( response.success === true ) {
						// Show success message.
						this.$parent.showNotice();

						// Update settings in DOM.
						this.$parent.updateSettings( response.data, 'email' );
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
