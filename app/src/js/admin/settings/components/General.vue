<template>
    <form id="general-settings" @submit="submitForm" action="options.php" method="post">

        <table class="form-table">
            <tbody>
            <tr>
                <th>
                    <label for="name">Name</label>
                </th>
                <td>
                    <input id="name" v-model="redirectType" type="text" name="name">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="age">Age</label>
                </th>
                <td>
                    <input id="age" v-model="redirectTo" type="number" name="age" min="0">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="movie">Favorite Movie</label>
                </th>
                <td>
                    <select id="movie" v-model="redirectPage" name="movie">
                        <option>Star Wars</option>
                        <option>Vanilla Sky</option>
                        <option>Atomic Blonde</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th colspan="2">
                    <input type="submit" name="submit" class="button button-primary" value="Save Changes" v-bind:disabled="waiting">
                </th>
            </tr>
            </tbody>
        </table>
    </form>
</template>

<script>
	export default {

		name: 'General',

		data() {
			return {
				alert: false,
				redirectType: null,
				redirectTo: null,
				redirectPage: null,
				redirectLink: null,
				redirectLog: null,
				emailNotification: null,
				disableGuessing: null,
				emailRecipient: null,
				excludePaths: null,
                waiting: false,
			}
		},

		methods: {
			/**
			 * Handle settings for submit.
			 *
			 * Validate the form before submitting it.
			 *
			 * @param e Event.
			 *
			 * @returns {boolean}
			 */
			submitForm: function ( e ) {
				this.showSuccess();
				this.waiting = true;

				// Do not submit form.
				e.preventDefault();
			},

			showSuccess: function () {
				this.$parent.alert = 'Thanks for the message, Joel.';

				setTimeout( () => {
					this.$parent.alert = false;
				}, 3000 );
			}
		}
	}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>

</style>
