<?php
/**
 * Admin settings page base template.
 *
 * @var array $menu_config Nav menu configuration.
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

<main class="max-w-7xl pb-10 lg:py-12 lg:px-8">
	<div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
		<?php $this->render( 'components/side-nav', $menu_config ); // Side nav menu. ?>

		<!-- Payment details -->
		<div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
			<section aria-labelledby="payment_details_heading">
				<form action="#" method="POST">
					<div class="shadow sm:rounded-md sm:overflow-hidden">
						<div class="bg-white py-6 px-4 sm:p-6">
							<div>
								<h2 id="payment_details_heading" class="text-lg leading-6 font-medium text-gray-900">
									<?php esc_html_e( 'General Settings', '404-to-301' ); ?>
								</h2>
								<p class="mt-1 text-sm text-gray-500">Update your billing information. Please note that updating your location could affect your tax rates.</p>
							</div>

							<div class="mt-6 grid grid-cols-4 gap-6">
								<div class="col-span-4 sm:col-span-2">
									<label for="first_name" class="block text-sm font-medium text-gray-700">First name</label>
									<input type="text" name="first_name" id="first_name" autocomplete="cc-given-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
								</div>

								<div class="col-span-4 sm:col-span-2">
									<label for="last_name" class="block text-sm font-medium text-gray-700">Last name</label>
									<input type="text" name="last_name" id="last_name" autocomplete="cc-family-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
								</div>

								<div class="col-span-4 sm:col-span-2">
									<label for="email_address" class="block text-sm font-medium text-gray-700">Email address</label>
									<input type="text" name="email_address" id="email_address" autocomplete="email" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
								</div>

								<div class="col-span-4 sm:col-span-1">
									<label for="expiration_date" class="block text-sm font-medium text-gray-700">Expration date</label>
									<input type="text" name="expiration_date" id="expiration_date" autocomplete="cc-exp" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm" placeholder="MM / YY">
								</div>

								<div class="col-span-4 sm:col-span-1">
									<label for="security_code" class="flex items-center text-sm font-medium text-gray-700">
										<span>Security code</span>
										<!-- Heroicon name: solid/question-mark-circle -->
										<svg class="ml-1 flex-shrink-0 h-5 w-5 text-gray-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
											<path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
										</svg>
									</label>
									<input type="text" name="security_code" id="security_code" autocomplete="cc-csc" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
								</div>

								<div class="col-span-4 sm:col-span-2">
									<label for="country" class="block text-sm font-medium text-gray-700">Country / Region</label>
									<select id="country" name="country" autocomplete="country" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
										<option>United States</option>
										<option>Canada</option>
										<option>Mexico</option>
									</select>
								</div>

								<div class="col-span-4 sm:col-span-2">
									<label for="postal_code" class="block text-sm font-medium text-gray-700">ZIP / Postal</label>
									<input type="text" name="postal_code" id="postal_code" autocomplete="postal-code" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
								</div>
							</div>
						</div>
						<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
							<button type="submit" class="bg-gray-800 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900">
								<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
									<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
									<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
								</svg>
								Saving
							</button>
						</div>
					</div>
				</form>
			</section>
		</div>
	</div>
</main>

