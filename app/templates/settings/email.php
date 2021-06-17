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
		<?php esc_html_e( 'Email Settings', '404-to-301' ); ?>
	</h2>
	<p class="mt-1 text-sm text-gray-500">Update your billing information. Please note that updating your location could affect your tax rates.</p>
</div>

<div class="pt-6">
	<ul class="mt-2 divide-y divide-gray-200">
		<li class="py-4 flex items-center justify-between">
			<div class="flex flex-col">
				<p class="text-sm font-medium text-gray-900" id="privacy-option-1-label">
					Email Notifications
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
	<label for="email" class="block text-sm font-medium text-gray-700">Email</label>
	<div class="mt-1 relative rounded-md shadow-sm">
		<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
			<!-- Heroicon name: solid/mail -->
			<svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
				<path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
				<path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
			</svg>
		</div>
		<input type="text" name="email" id="email" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="you@example.com">
	</div>
</div>

