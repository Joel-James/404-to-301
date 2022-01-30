<template>
	<div class="wrap duckdev-wrap">

		<div class="duckdev-top-wrap">
			<h1>
				{{ __('404 to 301', '404-to-301') }}
				<span class="subtitle">
					{{ __('by', '404-to-301') }} <a href="https://duckdev.com/?utm_source=dd4t3&utm_medium=plugin&utm_campaign=dd4t3_settings_header">Joel James</a> ( v{{$vars.version }} )
				</span>
			</h1>

		</div>

		<nav-tabs :items="tabs" active="redirect"/>

	</div>
</template>

<script>
import NavTabs from './components/nav/nav-tabs'

export default {

	components: {NavTabs},

	data() {
		return {
			tabs: [
				{
					name: 'redirect',
					label: this.__('Redirect', '404-to-301'),
					icon: 'randomize',
					component: '',
				},
				{
					name: 'logs',
					label: this.__('Logs', '404-to-301'),
					icon: 'media-default',
					component: '',
				},
				{
					name: 'email',
					label: this.__('Email', '404-to-301'),
					icon: 'email-alt',
					component: '',
				},
				{
					name: 'general',
					label: this.__('General', '404-to-301'),
					icon: 'admin-generic',
					component: '',
				},
				{
					name: 'info',
					label: this.__('Info', '404-to-301'),
					icon: 'info',
					component: '',
				}
			],
		}
	},

	computed: {

		/**
		 * Check if we can show the permissions tab.
		 *
		 * If statistics and permissions settings are not allowed
		 * by network admin on multisite, hide permissions tab.
		 *
		 * @since 3.2.4
		 * @since 3.2.5 Added settings permissions.
		 *
		 * @returns {boolean}
		 */
		showSubmit() {
			return false
		},
	},

	methods: {
		/**
		 * Save settings values using API.
		 *
		 * @since 3.2.4
		 */
		async saveSettings() {

		},
	},
}
</script>
