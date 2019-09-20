<template>
    <div class="email-settings">
        The Email Settings Page
    </div>
</template>

<script>
export default {

	name: 'Email',

	data () {
		return {
			redirectType: null,
			redirectTo: null,
			redirectPage: null,
			redirectLink: null,
			redirectLog: null,
			emailNotification: null,
			disableGuessing: null,
			emailRecipient: null,
			excludePaths: null,
		}
	}
};
</script>

<style lang="css" scoped>
</style>