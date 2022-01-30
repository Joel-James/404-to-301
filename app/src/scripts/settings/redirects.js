/* global WP_Smush */
/* global ajaxurl */

/**
 * CDN functionality.
 *
 * @since 3.0
 */
(($) => {
	'use strict';

	DD4t3Settings.Redirects = {
		settingsApp: $('#dd4t3-settings-app'),
		redirectsTab: $('#duckdev-tab-redirects-content'),

		init() {
			// No need to continue if required containers are not found.
			if (this.redirectsTab.length === 0 || this.settingsApp.length === 0) {
				return;
			}

			// Redirect target change.
			this.initTargetToggle()
		},

		/**
		 * Show/hide elements during status update in the updateStatsBox()
		 *
		 * @since 3.1  Moved out from updateStatsBox()
		 */
		initTargetToggle() {
			let page = this.redirectsTab.find('#redirect-target-page-container'),
				link = this.redirectsTab.find('#redirect-target-link-container')

			this.redirectsTab.on('change', '.redirect-target', (ev) => {
				let selected = $(ev.currentTarget).val()
				if ('page' === selected) {
					link.addClass('duckdev-hidden')
					page.removeClass('duckdev-hidden')
				} else {
					page.addClass('duckdev-hidden')
					link.removeClass('duckdev-hidden')
				}
			})
		},
	};

	DD4t3Settings.Redirects.init();
})(jQuery);
