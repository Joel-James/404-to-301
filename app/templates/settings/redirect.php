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

<div>
	<h2 id="dd404-settings-general-form-title" class="text-lg leading-6 font-medium text-gray-900">
		<?php esc_html_e( 'Redirect Settings', '404-to-301' ); ?>
	</h2>
	<p class="mt-1 text-sm text-gray-500">Update your billing information. Please note that updating your location could affect your tax rates.</p>
</div>

<div class="mt-6">
<fieldset>
	<legend class="text-sm font-medium text-gray-900">
		Redirect Type
	</legend>

	<div class="mt-1 bg-white rounded-md shadow-sm -space-y-px">
		<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
		<label class="bg-light-blue-50 border-light-blue-200 z-10 rounded-tl-md rounded-tr-md relative border p-4 flex cursor-pointer">
			<input checked="checked" type="radio" name="privacy_setting" value="Public access" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-0-label" aria-describedby="privacy-setting-0-description">
			<div class="ml-3 flex flex-col">
				<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
				<span id="privacy-setting-0-label" class="text-light-blue-900 block text-sm font-medium">
                301
              </span>
				<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
				<span id="privacy-setting-0-description" class="text-light-blue-700 block text-sm">
                This project would be available to anyone who has the link
              </span>
			</div>
		</label>

		<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
		<label class="border-gray-200 relative border p-4 flex cursor-pointer">
			<input type="radio" name="privacy_setting" value="Private to Project Members" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-1-label" aria-describedby="privacy-setting-1-description">
			<div class="ml-3 flex flex-col">
				<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
				<span id="privacy-setting-1-label" class="text-gray-900 block text-sm font-medium">
                404
              </span>
				<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
				<span id="privacy-setting-1-description" class="text-gray-500 block text-sm">
                Only members of this project would be able to access
              </span>
			</div>
		</label>

		<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
		<label class="border-gray-200 relative border p-4 flex cursor-pointer">
			<input type="radio" name="privacy_setting" value="Private to Project Members" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-1-label" aria-describedby="privacy-setting-1-description">
			<div class="ml-3 flex flex-col">
				<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
				<span id="privacy-setting-1-label" class="text-gray-900 block text-sm font-medium">
                302
              </span>
				<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
				<span id="privacy-setting-1-description" class="text-gray-500 block text-sm">
                Only members of this project would be able to access
              </span>
			</div>
		</label>

		<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
		<label class="border-gray-200 rounded-bl-md rounded-br-md relative border p-4 flex cursor-pointer">
			<input type="radio" name="privacy_setting" value="Private to you" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-2-label" aria-describedby="privacy-setting-2-description">
			<div class="ml-3 flex flex-col">
				<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
				<span id="privacy-setting-2-label" class="text-gray-900 block text-sm font-medium">
                307
              </span>
				<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
				<span id="privacy-setting-2-description" class="text-gray-500 block text-sm">
                You are the only one able to access this project
              </span>
			</div>
		</label>
	</div>
</fieldset>

	<fieldset>
		<legend class="text-sm font-medium text-gray-900">
			Redirect to
		</legend>

		<div class="mt-1 bg-white rounded-md shadow-sm -space-y-px">
			<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
			<label class="bg-light-blue-50 border-light-blue-200 z-10 rounded-tl-md rounded-tr-md relative border p-4 flex cursor-pointer">
				<input checked="checked" type="radio" name="privacy_setting" value="Public access" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-0-label" aria-describedby="privacy-setting-0-description">
				<div class="ml-3 flex flex-col">
					<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
					<span id="privacy-setting-0-label" class="text-light-blue-900 block text-sm font-medium">
                Existing page
              </span>
					<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
					<span id="privacy-setting-0-description" class="text-light-blue-700 block text-sm">
                This project would be available to anyone who has the link
              </span>
				</div>
			</label>

			<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
			<label class="border-gray-200 relative border p-4 flex cursor-pointer">
				<input type="radio" name="privacy_setting" value="Private to Project Members" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-1-label" aria-describedby="privacy-setting-1-description">
				<div class="ml-3 flex flex-col">
					<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
					<span id="privacy-setting-1-label" class="text-gray-900 block text-sm font-medium">
                Custom URL
              </span>
					<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
					<span id="privacy-setting-1-description" class="text-gray-500 block text-sm">
                Only members of this project would be able to access
              </span>
				</div>
			</label>

			<!-- Checked: "bg-light-blue-50 border-light-blue-200 z-10", Not Checked: "border-gray-200" -->
			<label class="border-gray-200 relative border p-4 flex cursor-pointer">
				<input type="radio" name="privacy_setting" value="Private to Project Members" class="h-4 w-4 mt-0.5 cursor-pointer text-light-blue-600 border-gray-300 focus:ring-light-blue-500" aria-labelledby="privacy-setting-1-label" aria-describedby="privacy-setting-1-description">
				<div class="ml-3 flex flex-col">
					<!-- Checked: "text-light-blue-900", Not Checked: "text-gray-900" -->
					<span id="privacy-setting-1-label" class="text-gray-900 block text-sm font-medium">
                No redirect
              </span>
					<!-- Checked: "text-light-blue-700", Not Checked: "text-gray-500" -->
					<span id="privacy-setting-1-description" class="text-gray-500 block text-sm">
                Only members of this project would be able to access
              </span>
				</div>
			</label>
		</div>
	</fieldset>
	<label for="first_name"
	       class="block text-sm font-medium text-gray-700"
	>URL</label>
	<input type="text" name="first_name" id="first_name" autocomplete="cc-given-name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">

	<label for="country" class="block text-sm font-medium text-gray-700">Country / Region</label>
	<select id="country" name="country" autocomplete="country" class="mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-gray-900 focus:border-gray-900 sm:text-sm">
		<option>United States</option>
		<option>Canada</option>
		<option>Mexico</option>
	</select>
</div>



