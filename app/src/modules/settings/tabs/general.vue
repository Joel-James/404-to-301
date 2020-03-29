<template>
    <form id="general-settings" @submit="submitForm" method="post">

        <table class="form-table">
            <tbody>
            <tr>
                <th><label for="redirectType">{{ $i18n.settings.labels.redirect_type }}</label></th>
                <td>
                    <select id="redirectType" v-model="redirectType">
                        <option value="301">{{ $i18n.settings.labels.redirect_301 }}</option>
                        <option value="302">{{ $i18n.settings.labels.redirect_302 }}</option>
                        <option value="307">{{ $i18n.settings.labels.redirect_307 }}</option>
                        <option value="404">{{ $i18n.settings.labels.redirect_404 }}</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="redirectTo">{{ $i18n.settings.labels.redirect_to }}</label></th>
                <td>
                    <select id="redirectTo" v-model="redirectTo">
                        <option value="page">{{ $i18n.settings.labels.existing_page }}</option>
                        <option value="link">{{ $i18n.settings.labels.custom_url }}</option>
                        <option value="none">{{ $i18n.settings.labels.no_redirect }}</option>
                    </select>
                    <p class="description" v-if="'page' === redirectTo">{{ $i18n.settings.descriptions.select_page }}</p>
                    <p class="description" v-if="'link' === redirectTo">{{ $i18n.settings.descriptions.custom_url }}</p>
                    <p class="description" v-if="'none' === redirectTo">{{ $i18n.settings.descriptions.disable_redirect }}</p>
                    <p class="description">
                        <strong>{{ $i18n.settings.descriptions.override_settings }}</strong>
                    </p>
                </td>
            </tr>
            <tr v-if="'page' === redirectTo">
                <th><label for="redirectPage">{{ $i18n.settings.labels.redirect_page }}</label></th>
                <td>
                    <select id="redirectPage" v-model="redirectPage">
                        <option value="page">{{ $i18n.settings.labels.existing_page }}</option>
                        <option value="link">{{ $i18n.settings.labels.custom_url }}</option>
                        <option value="none">{{ $i18n.settings.labels.no_redirect }}</option>
                    </select>
                </td>
            </tr>
            <tr v-if="'link' === redirectTo">
                <th><label for="redirectLink">{{ $i18n.settings.labels.custom_url }}</label></th>
                <td>
                    <input type="url" id="redirectLink" v-model="redirectLink">
                </td>
            </tr>
            <tr>
                <th><label for="redirectLog">{{ $i18n.settings.labels.log_errors }}</label></th>
                <td>
                    <input type="checkbox" id="redirectLog" v-model="redirectLog">
                </td>
            </tr>
            <tr>
                <th><label for="disableGuessing">{{ $i18n.settings.labels.disable_guess }}</label></th>
                <td>
                    <input type="checkbox" id="disableGuessing" v-model="disableGuessing">
                </td>
            </tr>
            <tr>
                <th><label for="excludePaths">{{ $i18n.settings.labels.exclude_paths }}</label></th>
                <td>
                    <textarea id="excludePaths" v-model="excludePaths"></textarea>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="submit"
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
				redirectType: this.$vars.settings.general.redirect_type,
				redirectTo: this.$vars.settings.general.redirect_to,
				redirectPage: this.$vars.settings.general.redirect_page,
				redirectLink: this.$vars.settings.general.redirect_link,
				redirectLog: this.$vars.settings.general.redirect_log,
				disableGuessing: this.$vars.settings.general.disable_guessing,
				excludePaths: this.$vars.settings.general.exclude_paths,
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
