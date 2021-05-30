<template>
	<form action="#" method="POST">
		<div class="shadow sm:rounded-md sm:overflow-hidden">
			<div class="bg-white py-6 px-4 space-y-6 sm:p-6">
				<ui-notification type="success">
					You have no credits left.
					<a
						href="#"
						class="font-medium underline text-green-700 hover:text-green-600"
					>
						Upgrade your account to add more credits.
					</a>
				</ui-notification>
				<div>
					<h3 class="text-lg leading-6 font-medium text-gray-900">
						General
					</h3>
					<p class="mt-1 text-sm text-gray-500">
						This information will be displayed publicly so be
						careful what you share.
					</p>
				</div>

				<div class="grid grid-cols-3 gap-6">
					<div class="col-span-3 sm:col-span-2">
						<div class="flex items-center justify-between">
							<span
								class="flex-grow flex flex-col"
								id="availability-label"
							>
								<span class="text-sm font-medium text-gray-900"
									>URL Guessing</span
								>
								<span class="text-xs mt-2 text-gray-500">
									If disabled, WordPress will stop
									<a
										href=""
										class="text-wpblue-700 hover:text-wpblue-800"
										>autocorrecting incorrect URLs</a
									>.
								</span>
							</span>
							<ui-toggle
								v-model="guessUrl"
								label="Disable Guessing"
							/>
						</div>
					</div>

					<div class="col-span-3">
						<label
							for="about"
							class="text-sm font-medium text-gray-900"
						>
							About
						</label>
						<p class="my-2 text-xs text-gray-500">
							Brief description for your profile. URLs are
							hyperlinked.
						</p>
						<div class="mt-1">
							<textarea
								id="about"
								name="about"
								rows="3"
								class="shadow-sm focus:ring-wpblue-700 focus:border-wpblue-700 mt-1 block w-full sm:text-sm border-gray-300 rounded-md"
								placeholder="you@example.com"
							></textarea>
						</div>
					</div>
				</div>
			</div>
			<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
				<ui-save-button />
			</div>
		</div>
	</form>
</template>

<script>
import UiToggle from '@/components/ui-toggle'
import UiSaveButton from '@/components/ui-save-button'
import UiNotification from '@/components/ui-notification'

export default {
	name: 'GeneralSettings',

	components: {
		UiToggle,
		UiSaveButton,
		UiNotification,
	},

	computed: {},

	data() {
		return {
			enableGuessing: false,
			guessUrl: true,
		}
	},
}
</script>

<style lang="scss">
@import '../styles/main';
</style>
