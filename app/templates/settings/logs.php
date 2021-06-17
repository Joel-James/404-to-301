<?php
/**
 * Admin settings general tab template.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @subpackage Pages
 */

?>

<div>
	<h2 id="dd404-settings-general-form-title" class="text-lg leading-6 font-medium text-gray-900">
		<?php esc_html_e( 'Logs Settings', '404-to-301' ); ?>
	</h2>
	<p class="mt-1 text-sm text-gray-500">Update your billing information. Please note that updating your location could affect your tax rates.</p>
</div>

<div class="pt-6 divide-y">
	<ul class="mt-2 divide-y divide-gray-200">
		<li class="py-4 flex items-center justify-between">
			<div class="flex flex-col">
				<p class="text-sm font-medium text-gray-900" id="privacy-option-1-label">
					Log 404 Errors
				</p>
				<p class="text-sm text-gray-500" id="privacy-option-1-description">
					Nulla amet tempus sit accumsan. Aliquet turpis sed sit lacinia.
				</p>
			</div>
			<!-- Enabled: "bg-teal-500", Not Enabled: "bg-gray-200" -->
			<button type="button" class="bg-wpblue-500 ml-4 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light-blue-500" role="switch" aria-checked="true" aria-labelledby="privacy-option-1-label" aria-describedby="privacy-option-1-description">
				<span class="sr-only">Use setting</span>
				<!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
				<span aria-hidden="true" class="translate-x-5 inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"></span>
			</button>
		</li>
	</ul>
</div>

