<template>
	<!-- Enable redirects -->
	<h4>{{ __('Enable Redirects', '404-to-301') }}</h4>
	<p>{{ __('Do you want to redirect the 404 errors to a new page or URL?', '404-to-301') }}</p>
	<p
		v-html="sprintf(
			__('These options can be customized for each individual 404 errors from %sthe logs page%s.', '404-to-301'),
			'<a href="">',
	'</a>'
	)"
	></p>
	<div class="duckdev-fields">
		<label for="redirect-enabled">
			<input
				type="checkbox"
				id="redirect-enabled"
				v-model="redirectEnabled"
			/>
			{{ __('Enable redirects for 404 errors', '404-to-301') }}
		</label>
	</div>

	<hr>

	<!-- Redirect type -->
	<h4>{{ __('Redirect type', '404-to-301') }}</h4>
	<p>{{
			__('The redirect type is the HTTP response code sent to the browser telling the browser what type of redirect is served.', '404-to-301') }}</p>
	<p v-html="sprintf(
				__('Learn more about HTTP redirect types on %sMDN Web Docs%s before you select decide the type.', '404-to-301'),
				'<a href="https://developer.mozilla.org/en-US/docs/Web/HTTP/Redirections" target="_blank">',
		'</a>'
		)"
	>
	</p>

	<div class="duckdev-fields">
		<p
			v-for="(label, type) in types"
			:key="type"
		>
			<label :for="`redirect-type-${type}`">
				<input
					type="radio"
					:id="`redirect-type-${type}`"
					:value="type"
					v-model="redirectType"
				> {{ label }}
			</label>
		</p>
	</div>

	<hr>

	<!-- Redirect target -->
	<h4>{{ __('Target', '404-to-301') }}</h4>
	<p>{{ __('From the target types, choose where you want to redirect the 404 errors to.', '404-to-301') }}</p>

	<div class="duckdev-fields">
		<p>
			<label for="redirect-target-page">
				<input
					type="radio"
					id="redirect-target-page"
					value="page"
					v-model="redirectTarget"
				/>
				{{ __('Select an existing page on this website', '404-to-301') }}
			</label>
		</p>
		<p>
			<label for="redirect-target-link">
				<input
					type="radio"
					id="redirect-target-link"
					value="link"
					v-model="redirectTarget"
				/>
				{{ __('Enter a custom URL', '404-to-301') }}
			</label>
		</p>
	</div>

	<!-- Redirect page -->
	<div class="duckdev-fields" v-if="'page' === redirectTarget">
		<p>
			<label for="redirect-target-page-value">
				<strong>{{ __('Select page', '404-to-301') }}</strong>
			</label>
		</p>
		<p>

		</p>
	</div>

	<!-- Redirect link -->
	<div class="duckdev-fields" v-else-if="'link' === target">
		<p>
			<label for="redirect-target-link-value">
				<strong>{{ __('Custom URL', '404-to-301') }}</strong>
			</label>
		</p>
		<p>
			<input
				type="url"
				id="redirect-target-link-value"
				class="large-text"
				placeholder="https://example.com"
				v-model="redirectLink"
			>
		</p>
	</div>
</template>

<script>
export default {
	name: 'TabEmail',

	props: {},

	data() {
		return {
			redirectType: '301',
			redirectEnabled: false,
			redirectTarget: 'link',
			redirectLink: '',
			types: this.$vars.types
		}
	},

	methods: {}
}
</script>
