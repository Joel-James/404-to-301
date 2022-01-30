/* global WP_Smush */
/* global ajaxurl */

/**
 * CDN functionality.
 *
 * @since 3.0
 */
(function ($) {
	'use strict';

	DD4t3Settings.Notifications = {
		settingsApp: $('#dd4t3-settings-app'),
		cdnDisableButton: $('#duckdev-tab-notifications-content'),

		init() {
		},

		/**
		 * Show/hide elements during status update in the updateStatsBox()
		 *
		 * @since 3.1  Moved out from updateStatsBox()
		 */
		toggleElements() {
		},
	};

	DD4t3Settings.Notifications.init();
})(jQuery);
