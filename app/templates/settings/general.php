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
		<?php esc_html_e( 'General Settings', '404-to-301' ); ?>
	</h2>
	<p class="mt-1 text-sm text-gray-500">Update your billing information. Please note that updating your location could affect your tax rates.</p>
</div>

<div class="pt-6 divide-y">
	<ul class="mt-2 divide-y divide-gray-200">
		<li class="py-4 flex items-center justify-between">
			<div class="flex flex-col">
				<p class="text-sm font-medium text-gray-900" id="privacy-option-1-label">
					Disable URL Guessing
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
		<li class="py-4 flex items-center justify-between">
			<div class="flex flex-col">
				<p class="text-sm font-medium text-gray-900" id="privacy-option-1-label">
					Monitor Permalink Changes
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
	<div class="space-y-1">
		<label for="add_team_members" class="block text-sm font-medium text-gray-700">
			Exclude Path
		</label>
		<p id="add_team_members_helper" class="sr-only">Search by email address</p>
		<div class="flex">
			<div class="flex-grow">
				<input type="text" name="first_name" id="first_name" autocomplete="cc-given-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
			</div>
			<span class="ml-3">
              <button type="button" class="bg-white inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-light-blue-500">
                <!-- Heroicon name: solid/plus -->
                <svg class="-ml-2 mr-1 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                  <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                <span>Add</span>
              </button>
            </span>
		</div>
		<!-- This example requires Tailwind CSS v2.0+ -->
		<div class="sm:col-span-2">
			<dt class="text-sm font-medium text-gray-500">
				Attachments
			</dt>
			<dd class="mt-1 text-sm text-gray-900">
				<ul class="border border-gray-200 rounded-md divide-y divide-gray-200">

					<li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
						<div class="w-0 flex-1 flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
							</svg>
							<span class="text-gray-600 ml-2 flex-1 w-0 truncate">
                                /wp-content/*
                              </span>
						</div>
						<div class="ml-4 flex-shrink-0">
							<button type="button" class="bg-white h-5 w-5 flex items-center justify-center text-red-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-none">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
								</svg>
								<span class="sr-only">Remove</span>
							</button>
						</div>
					</li>

					<li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
						<div class="w-0 flex-1 flex items-center">
							<svg xmlns="http://www.w3.org/2000/svg" class="flex-shrink-0 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
							</svg>
							<span class="text-gray-600 ml-2 flex-1 w-0 truncate">
                                /test-url/
                              </span>
						</div>
						<div class="ml-4 flex-shrink-0">
							<button type="button" class="bg-white h-5 w-5 flex items-center justify-center text-red-400 hover:text-red-500 focus:outline-none focus:ring-2 focus:ring-none">
								<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
									<path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
								</svg>
								<span class="sr-only">Remove</span>
							</button>
						</div>
					</li>

				</ul>
			</dd>
		</div>

	</div>
</div>

