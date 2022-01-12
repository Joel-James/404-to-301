<template>
	<p>Hey %s. Thank you for using 404 to 301. You can configure how 404 to 301 plugin should handle 404 errors on your website.</p>

	<div class="duckdev-tabs-content duckdev-active-tab-content" id="duckdev-tab-<?php echo esc_attr( $page ); ?>-content">

		<p>
			<label for="email-enabled">
				<strong>{{ __('Enable Email', '404-to-301') }}</strong>
			</label>
		</p>
		<p>{{ __('Do you want to receive and email notification for each 404 errors?', '404-to-301') }}</p>
		<p>{{ __('You will get an email notification for every single 404 error. Please think twice if your site is getting 100s of 404s everyday!', '404-to-301') }}</p>
		<div class="duckdev-fields">
			<label for="email-enabled">
				<input
					type="checkbox"
					name="404_to_301_settings[email_enable]"
					id="email-enabled"
					value="1"
				> {{ __('Enable email notifications for 404 errors?', '404-to-301') }}
			</label>
		</div>

		<hr/>

		<p>
			<label for="email-recipient">
				<strong>{{ __('Recipient email', '404-to-301') }}</strong>
			</label>
		</p>
		<p>{{ __('Enter the email address where you want to get the email notification.', '404-to-301') }}</p>
		<p>
			<input
				type="email"
				name="404_to_301_settings[email_recipient]"
				id="email-recipient"
				class="regular-text"
				placeholder="admin@duckdev.com"
				value=""
			>
		</p>

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
