<template>
	<fieldset>
		<legend class="sr-only">{{ title }}</legend>
		<div class="space-y-4">
			<label
				v-for="item in items"
				class="relative block rounded-lg border border-gray-300 bg-white shadow-sm px-6 py-4 cursor-pointer hover:border-gray-400 sm:flex sm:justify-between"
			>
				<input
					type="radio"
					name="redirect[type]"
					:value="item.key"
					v-model="selected"
					class="sr-only"
					aria-labelledby="server-size-0-label"
					aria-describedby="server-size-0-description-0 server-size-0-description-1"
				>
				<div class="flex items-center">
					<div class="text-sm">
						<p
							id="server-size-0-label"
							class="font-medium text-gray-900"
						>
							{{ item.label }}
						</p>
						<div
							id="server-size-0-description-0"
							class="text-gray-500"
						>
							<p class="sm:inline">{{ item.desc }}</p>
							<p class="sm:inline">{{ selected }}</p>
						</div>
					</div>
				</div>
				<div
					:class="selected === item.key ? 'border-gray-900' : 'border-transparent'"
					class="absolute -inset-px rounded-lg border-2 pointer-events-none"
					aria-hidden="true"
				></div>
			</label>
		</div>
	</fieldset>
</template>

<script>
export default {
	name: 'FormRadioSelect',

	props: {
		title: String,
		items: Array,
		current: String,
	},

	data() {
		return {
			selected: this.current
		}
	},
}
</script>
