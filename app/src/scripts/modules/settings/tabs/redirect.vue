<template>
	<form action="#" method="POST">
		<div class="shadow sm:rounded-md sm:overflow-hidden">
			<div class="bg-white py-6 px-4 space-y-6 sm:p-6">
				<div>
					<h2
						id="plan_heading"
						class="text-lg leading-6 font-medium text-gray-900"
					>
						Plan
					</h2>
				</div>

				<fieldset>
					<legend class="sr-only">
						Pricing plans
					</legend>
					<ul class="relative bg-white rounded-md -space-y-px">
						<li>
							<!-- On: "bg-orange-50 border-orange-200 z-10", Off: "border-gray-200" -->
							<div
								class="relative border border-gray-200 rounded-tl-md rounded-tr-md p-4 flex flex-col md:pl-4 md:pr-6 md:grid md:grid-cols-3"
							>
								<label
									class="flex items-center text-sm cursor-pointer"
								>
									<input
										name="pricing_plan"
										type="radio"
										class="h-4 w-4 text-orange-500 cursor-pointer focus:ring-gray-900 border-gray-300"
										aria-describedby="plan-option-pricing-0 plan-option-limit-0"
									/>
									<span class="ml-3 font-medium text-gray-900"
										>Startup</span
									>
								</label>
								<p
									id="plan-option-pricing-0"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-center"
								>
									<!-- On: "text-orange-900", Off: "text-gray-900" -->
									<span class="font-medium text-gray-900"
										>$29 / mo</span
									>
									<!-- On: "text-orange-700", Off: "text-gray-500" -->
									<span class="text-gray-500"
										>($290 / yr)</span
									>
								</p>
								<!-- On: "text-orange-700", Off: "text-gray-500" -->
								<p
									id="plan-option-limit-0"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-right text-gray-500"
								>
									Up to 5 active job postings
								</p>
							</div>
						</li>

						<li>
							<!-- On: "bg-orange-50 border-orange-200 z-10", Off: "border-gray-200" -->
							<div
								class="relative border bg-orange-50 border-orange-200 z-10 p-4 flex flex-col md:pl-4 md:pr-6 md:grid md:grid-cols-3"
							>
								<label
									class="flex items-center text-sm cursor-pointer"
								>
									<input
										name="pricing_plan"
										type="radio"
										class="h-4 w-4 text-orange-500 cursor-pointer focus:ring-gray-900 border-gray-300"
										aria-describedby="plan-option-pricing-1 plan-option-limit-1"
										checked
									/>
									<span class="ml-3 font-medium text-gray-900"
										>Business</span
									>
								</label>
								<p
									id="plan-option-pricing-1"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-center"
								>
									<!-- On: "text-orange-900", Off: "text-gray-900" -->
									<span class="font-medium text-orange-900"
										>$99 / mo</span
									>
									<!-- On: "text-orange-700", Off: "text-gray-500" -->
									<span class="text-orange-700"
										>($990 / yr)</span
									>
								</p>
								<!-- On: "text-orange-700", Off: "text-gray-500" -->
								<p
									id="plan-option-limit-1"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-right text-orange-700"
								>
									Up to 25 active job postings
								</p>
							</div>
						</li>

						<li>
							<!-- On: "bg-orange-50 border-orange-200 z-10", Off: "border-gray-200" -->
							<div
								class="relative border border-gray-200 rounded-bl-md rounded-br-md p-4 flex flex-col md:pl-4 md:pr-6 md:grid md:grid-cols-3"
							>
								<label
									class="flex items-center text-sm cursor-pointer"
								>
									<input
										name="pricing_plan"
										type="radio"
										class="h-4 w-4 text-orange-500 cursor-pointer focus:ring-gray-900 border-gray-300"
										aria-describedby="plan-option-pricing-2 plan-option-limit-2"
									/>
									<span class="ml-3 font-medium text-gray-900"
										>Enterprise</span
									>
								</label>
								<p
									id="plan-option-pricing-2"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-center"
								>
									<!-- On: "text-orange-900", Off: "text-gray-900" -->
									<span class="font-medium text-gray-900"
										>$249 / mo</span
									>
									<!-- On: "text-orange-700", Off: "text-gray-500" -->
									<span class="text-gray-500"
										>($2490 / yr)</span
									>
								</p>
								<!-- On: "text-orange-700", Off: "text-gray-500" -->
								<p
									id="plan-option-limit-2"
									class="ml-6 pl-1 text-sm md:ml-0 md:pl-0 md:text-right text-gray-500"
								>
									Unlimited active job postings
								</p>
							</div>
						</li>
					</ul>
				</fieldset>

				<div class="flex items-center">
					<!-- Enabled: "bg-orange-500", Not Enabled: "bg-gray-200" -->
					<button
						type="button"
						class="bg-gray-200 relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors ease-in-out duration-200"
						aria-pressed="true"
						aria-labelledby="annual-billing-label"
					>
						<span class="sr-only">Use setting</span>
						<!-- Enabled: "translate-x-5", Not Enabled: "translate-x-0" -->
						<span
							aria-hidden="true"
							class="translate-x-0 inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200"
						></span>
					</button>
					<span class="ml-3" id="annual-billing-label">
						<span class="text-sm font-medium text-gray-900"
							>Annual billing
						</span>
						<span class="text-sm text-gray-500">(Save 10%)</span>
					</span>
				</div>
			</div>
			<div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
				<button
					type="submit"
					class="bg-gray-800 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900"
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
	name: 'RedirectSettings',

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
