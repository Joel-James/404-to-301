<template>
    <div id="dd404-admin-settings">

        <div v-if="alert" class="notice notice-success is-dismissible">
            <p>{{ alert }}</p>
        </div>

        <nav class="nav-tab-wrapper">
            <router-link to="/" class="nav-tab" exact>
                <span class="dashicons dashicons-admin-generic"></span>
                General
            </router-link>
            <router-link to="/email" class="nav-tab">
                <span class="dashicons dashicons-email"></span>
                Email
            </router-link>
        </nav>
        <router-view/>
    </div>
</template>

<script>
	export default {
		name: 'SettingsApp',
		data() {
			return {
				alert: false,
			}
		},
	}
</script>

<style>

</style>
