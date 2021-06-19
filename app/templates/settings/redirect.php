<?php
/**
 * Admin settings redirect tab template.
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

<div class="space-y-8 divide-y divide-gray-200 sm:space-y-5">

	<div>
		<div>
			<h3 class="text-lg leading-6 font-medium text-gray-900">
				Notifications
			</h3>
			<p class="mt-1 max-w-2xl text-sm text-gray-500">
				We'll always let you know about important changes, but you pick what else you want to hear about.
			</p>
		</div>
		<div class="space-y-6 sm:space-y-5 divide-y divide-gray-200">
			<div class="pt-6 sm:pt-5">
				<div role="group" aria-labelledby="label-notifications">
					<div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
						<div>
							<div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-notifications">
								Push Notifications
							</div>
						</div>
						<div class="sm:col-span-2">
							<div class="max-w-lg">
								<p class="text-sm text-gray-500">These are delivered via SMS to your mobile phone.</p>
								<div class="mt-4 space-y-4">
									<form-radio-select
										title="<?php esc_html_e( 'Redirect type', '404-to-301' ); ?>"
										:items="targets"
										current="301"
									/>

								</div>
							</div>
						</div>
					</div>
					<div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
						<div>
							<div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-notifications">
								URL
							</div>
						</div>
						<div class="sm:col-span-2">
							<div class="max-w-lg">
								<p class="text-sm text-gray-500">These are delivered via SMS to your mobile phone.</p>
								<div class="mt-4 space-y-4">
									<label for="first_name"
									       class="block text-sm font-medium text-gray-700"
									>URL</label>
									<input type="text" name="first_name" id="first_name" autocomplete="cc-given-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">

								</div>
							</div>
						</div>
					</div>
					<div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
						<div>
							<div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-notifications">
								Page
							</div>
						</div>
						<div class="sm:col-span-2">
							<div class="max-w-lg">
								<p class="text-sm text-gray-500">These are delivered via SMS to your mobile phone.</p>
								<div class="mt-4 space-y-4">
									<label for="country" class="block text-sm font-medium text-gray-700">Country / Region</label>
									<select id="country" name="country" autocomplete="country" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
										<option>United States</option>
										<option>Canada</option>
										<option>Mexico</option>
									</select>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="pt-6 sm:pt-5">
				<div role="group" aria-labelledby="label-email">
					<div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
						<div>
							<div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-email">
								By Email
							</div>
						</div>
						<div class="mt-4 sm:mt-0 sm:col-span-2">
							<div class="max-w-lg">
								<p class="text-sm text-gray-500">These are delivered via SMS to your mobile phone.</p>
								<div class="mt-4 space-y-4">
									<form-radio-select
										title="<?php esc_html_e( 'Redirect type', '404-to-301' ); ?>"
										:items="types"
										current="301"
									/>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>