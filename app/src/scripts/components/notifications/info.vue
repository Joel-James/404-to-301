<template>
	<div class="bg-light-blue-50 border-l-4 border-light-blue-400 p-4 my-2">
		<div class="flex">
			<div class="flex-shrink-0">
				<!-- Heroicon name: solid/information-circle -->
				<svg
					class="h-5 w-5 text-blue-400"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 20 20"
					fill="currentColor"
					aria-hidden="true"
				>
					<path
						fill-rule="evenodd"
						d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
						clip-rule="evenodd"
					/>
				</svg>
			</div>
			<div class="ml-3">
				<p class="text-sm text-light-blue-700">
					<slot></slot>
				</p>
			</div>
		</div>
	</div>
</template>
