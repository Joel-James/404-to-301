<template>
    <div class="notice notice-success is-dismissible">
        <p>{{ message }}</p>
    </div>
</template>

<script>
	export default {

		name: 'Logs',

		data() {
			return {
				message: 'Error logs logs will be here soon.'
            }
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
