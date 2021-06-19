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
					<div role="group" aria-labelledby="label-email">
						<div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-baseline">
							<div>
								<div class="text-base font-medium text-gray-900 sm:text-sm sm:text-gray-700" id="label-email">
									By Email
								</div>
							</div>
							<div class="mt-4 sm:mt-0 sm:col-span-2">
								<div class="max-w-lg space-y-4">
									<div class="relative flex items-start">
										<div class="flex items-center h-5">
											<input id="comments" name="comments" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
										</div>
										<div class="ml-3 text-sm">
											<label for="comments" class="font-medium text-gray-700">Comments</label>
											<p class="text-gray-500">Get notified when someones posts a comment on a posting.</p>
										</div>
									</div>
									<div>
										<div class="relative flex items-start">
											<div class="flex items-center h-5">
												<input id="candidates" name="candidates" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
											</div>
											<div class="ml-3 text-sm">
												<label for="candidates" class="font-medium text-gray-700">Candidates</label>
												<p class="text-gray-500">Get notified when a candidate applies for a job.</p>
											</div>
										</div>
									</div>
									<div>
										<div class="relative flex items-start">
											<div class="flex items-center h-5">
												<input id="offers" name="offers" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
											</div>
											<div class="ml-3 text-sm">
												<label for="offers" class="font-medium text-gray-700">Offers</label>
												<p class="text-gray-500">Get notified when a candidate accepts or rejects an offer.</p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
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
											:items="items"
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


