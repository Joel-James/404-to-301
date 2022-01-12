<template>
	<!-- Enable logs -->
	<h4>{{ __('Enable Logs', '404-to-301') }}</h4>
	<p>{{ __('Do you want to log the 404 errors in detail?', '404-to-301') }}</p>
	<p>{{ __('This will be helpful for you to keep track of broken links to your website. You can also setup individual redirects for each 404s from the logs page.', '404-to-301') }}</p>
	<div class="duckdev-fields">
		<label for="logs-enabled">
			<input
				type="checkbox"
				name="404_to_301_settings[logs_enabled]"
				id="logs-enabled"
				value="1"
				@change="toggleLogs"
			/> {{ __('Enable logs for 404 errors', '404-to-301') }}
		</label>
	</div>

	<hr/>

	<!-- Duplicate logs -->
	<h4>{{ __('Handling Duplicate Logs', '404-to-301') }}</h4>
	<p>{{ __('You may get 100s of visits to an old or non-existing link on your website. This can create 100s of copies of the same 404 link. If you enable this, the duplicates will be skipped without affecting the redirects. This will be helpful to keep your database light.', '404-to-301') }}</p>
	<p>{{ __('Please note: Only the 404 url will be checked for duplicates. Visitor details can be different, but still it will be counted as duplicate.', '404-to-301') }}</p>
	<div class="duckdev-fields">
		<label for="logs-skip-duplicates">
			<input
				type="checkbox"
				name="404_to_301_settings[logs_skip_duplicates]"
				id="logs-skip-duplicates"
				value="1"
			/> {{ __('Skip duplicate entries from the logs', '404-to-301') }}
		</label>
	</div>
</template>

<script>
export default {
	name: 'TabEmail',

	props: {},

	data() {
		return {}
	},

	methods: {}
}
</script>
