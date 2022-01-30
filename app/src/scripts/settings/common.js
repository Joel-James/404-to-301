/* global WP_Smush */
/* global ajaxurl */

/**
 * Common functionality in settings page.
 *
 * @since 4.0.0
 */
(function ($) {
	'use strict';

	DD4t3Settings.Common = {
		settingsApp: $('#dd4t3-settings-app'),
		submitButton: $('#dd4t3-settings-submit'),

		init() {
			// No need to continue if required containers are not found.
			if (this.settingsApp.length === 0 || this.submitButton.length === 0) {
				return;
			}

			// Form submit.
			this.initSubmitClick()
		},

		/**
		 * Show/hide elements during status update in the updateStatsBox()
		 *
		 * @since 3.1  Moved out from updateStatsBox()
		 */
		initSubmitClick() {
			this.submitButton.on('click', (ev) => {
				$(ev.currentTarget)
				// Change text.
				.text(wp.i18n.__('Saving Changes..', '404-to-301'))
				// Disable button.
				.prop('disabled', true)
			})
		},
	};

	DD4t3Settings.Common.init();
})(jQuery);
