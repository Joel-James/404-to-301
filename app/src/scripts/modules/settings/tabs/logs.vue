<template>
	<form action="#" method="POST">
		<div class="shadow sm:rounded-md sm:overflow-hidden">
			<div class="bg-white py-6 px-4 space-y-6 sm:p-6">
				<div>
					<h3 class="text-lg leading-6 font-medium text-gray-900">
						Logs
					</h3>
					<p class="mt-1 text-sm text-gray-500">
						This information will be displayed publicly so be
						careful what you share.
					</p>
				</div>

				<div class="grid grid-cols-3 gap-6">
					<div class="col-span-3 sm:col-span-2">
						<label
							for="company_website"
							class="block text-sm font-medium text-gray-700"
						>
							Username
						</label>
						<div class="mt-1 rounded-md shadow-sm flex">
							<span
								class="bg-gray-50 border border-r-0 border-gray-300 rounded-l-md px-3 inline-flex items-center text-gray-500 sm:text-sm"
							>
								workcation.com/
							</span>
							<input
								type="text"
								name="username"
								id="username"
								autocomplete="username"
								class="focus:ring-indigo-500 focus:border-indigo-500 flex-grow block w-full min-w-0 rounded-none rounded-r-md sm:text-sm border-gray-300"
							/>
						</div>
					</div>

					<div class="col-span-3">
						<label
							for="about"
							class="block text-sm font-medium text-gray-700"
						>
							About
						</label>
						<div class="mt-1">
							<textarea
								id="about"
								name="about"
								rows="3"
								class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
								placeholder="you@example.com"
							></textarea>
						</div>
						<p class="mt-2 text-sm text-gray-500">
							Brief description for your profile. URLs are
							hyperlinked.
						</p>
					</div>

					<div class="col-span-3">
						<label class="block text-sm font-medium text-gray-700">
							Photo
						</label>
						<div class="mt-1 flex items-center">
							<span
								class="inline-block bg-gray-100 rounded-full overflow-hidden h-12 w-12"
							>
								<svg
									class="h-full w-full text-gray-300"
									fill="currentColor"
									viewBox="0 0 24 24"
								>
									<path
										d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z"
									/>
								</svg>
							</span>
							<button
								type="button"
								class="ml-5 bg-white border border-gray-300 rounded-md shadow-sm py-2 px-3 text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
							>
								Change
							</button>
						</div>
					</div>

					<div class="col-span-3">
						<label class="block text-sm font-medium text-gray-700">
							Cover photo
						</label>
						<div
							class="mt-1 border-2 border-gray-300 border-dashed rounded-md px-6 pt-5 pb-6 flex justify-center"
						>
							<div class="space-y-1 text-center">
								<svg
									class="mx-auto h-12 w-12 text-gray-400"
									stroke="currentColor"
									fill="none"
									viewBox="0 0 48 48"
									aria-hidden="true"
								>
									<path
										d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
										stroke-width="2"
										stroke-linecap="round"
										stroke-linejoin="round"
									/>
								</svg>
								<div class="flex text-sm text-gray-600">
									<label
										for="file-upload"
										class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500"
									>
										<span>Upload a file</span>
										<input
											id="file-upload"
											name="file-upload"
											type="file"
											class="sr-only"
										/>
									</label>
									<p class="pl-1">
										or drag and drop
									</p>
								</div>
								<p class="text-xs text-gray-500">
									PNG, JPG, GIF up to 10MB
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
				<button
					type="submit"
					class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
				>
					Save
				</button>
			</div>
		</div>
	</form>
</template>

<script>
import UiSaveButton from '@/components/ui-save-button'

export default {
	name: 'LogsSettings',

	components: {
		UiSaveButton,
	},

	computed: {},

	data() {
		return {}
	},
}
</script>
<style lang="scss">
@import '../styles/main';
</style>
