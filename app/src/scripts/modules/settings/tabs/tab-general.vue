<template>
	<!-- URL guessing -->
	<h4>{{ __('URL Guessing', '404-to-301') }}</h4>
	<p>{{
			__('WordPress will automatically correct a 404 URL if it is misspelled and very close to an existing link, before marking it as a 404 error.', '404-to-301') }}</p>
	<p v-html="sprintf(
				__('This happens as part of canonical redirect feature. You can learn more about %sthe canonical redirect%s in WordPress.', '404-to-301'),
	'<a href=\"https://developer.wordpress.org/reference/functions/redirect_canonical\" target=\"_blank\">',
	'</a>'
	)"
	>
	</p>
	<div class="duckdev-fields">
		<label for="disable-guessing">
			<input
				type="checkbox"
				name="404_to_301_settings[disable_guessing]"
				id="disable-guessing"
				value="1"
			> {{ __('Stop WordPress from guessing URLs', '404-to-301') }}
		</label>
	</div>

	<hr/>

	<!-- Permalink changes -->
	<h4>{{ __('Permalink Changes', '404-to-301') }}</h4>
	<p>{{
			__('New 404 errors can be created when you change an existing page/post permalink to a new one. Instead of waiting for someone to visit and create a 404 error, ww can create a redirect ourself to the new permalink.', '404-to-301') }}</p>
	<div class="duckdev-fields">
		<label for="monitor-changes">
			<input
				type="checkbox"
				name="404_to_301_settings[monitor_changes]"
				id="monitor-changes"
				value="1"
			> {{ __('Monitor permalink changes and create redirects', '404-to-301') }}
		</label>
	</div>

	<hr/>

	<!-- GDPR settings -->
	<h4>{{ __('GDPR & Privacy', '404-to-301') }}</h4>
	<p>{{
			__('To respect visitor\'s privacy and comply with GDPR policies, you may disable a few functionalities of the plugin.', '404-to-301') }}</p>
	<div class="duckdev-fields">
		<label for="logs-ip-logging">
			<input
				type="checkbox"
				name="404_to_301_settings[ip_logging]"
				id="logs-ip-logging"
				value="1"
			> {{ __('Do not log visitor\'s IP address', '404-to-301') }}
		</label>
	</div>

	<hr/>

	<!-- URL exclusions -->
	<h4>{{ __('Exclusions', '404-to-301') }}</h4>
	<p v-html="sprintf(
				__('Use this option to exclude a URL from being detected as 404 by the plugin. It will be wildcard checked using %s for a match.', '404-to-301'),
				'<code><a href=\"https://www.php.net/manual/en/function.strpos.php\" target=\"_blank\">strpos</a></code>'
	)"
	>
	</p>
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
