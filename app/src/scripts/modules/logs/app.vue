<template>
	<div class="container mt-5 mr-20 ml-2">
		<h2 class="text-3xl font-bold text-black-600">
			Error Logs
		</h2>
		<!-- This example requires Tailwind CSS v2.0+ -->
		<div class="flex flex-col pt-5">
			<div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
				<div
					class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8"
				>
					<div
						class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg"
					>
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										Name
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										Title
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										Email
									</th>
									<th
										scope="col"
										class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
									>
										Role
									</th>
									<th scope="col" class="relative px-6 py-3">
										<span class="sr-only">Edit</span>
									</th>
								</tr>
							</thead>
							<tbody>
								<!-- Odd row -->
								<tr class="bg-white">
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										Jane Cooper
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										Regional Paradigm Technician
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										jane.cooper@example.com
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										Admin
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
									>
										<a
											href="#"
											class="text-indigo-600 hover:text-indigo-900"
											>Edit</a
										>
									</td>
								</tr>

								<!-- Even row -->
								<tr class="bg-gray-50">
									<td
										class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
									>
										Cody Fisher
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										Product Directives Officer
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										cody.fisher@example.com
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
									>
										Owner
									</td>
									<td
										class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
									>
										<a
											href="#"
											class="text-indigo-600 hover:text-indigo-900"
											>Edit</a
										>
									</td>
								</tr>

								<!-- More items... -->
							</tbody>
						</table>
						<nav
							class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6"
							aria-label="Pagination"
						>
							<div class="hidden sm:block">
								<p class="text-sm text-gray-700">
									Showing
									<span class="font-medium">1</span>
									to
									<span class="font-medium">10</span>
									of
									<span class="font-medium">20</span>
									results
								</p>
							</div>
							<div
								class="flex-1 flex justify-between sm:justify-end"
							>
								<a
									href="#"
									class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
								>
									Previous
								</a>
								<a
									href="#"
									class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
								>
									Next
								</a>
							</div>
						</nav>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'App',

	components: {},

	computed: {},
}
</script>

<style lang="scss">
@import 'styles/main';
</style>
