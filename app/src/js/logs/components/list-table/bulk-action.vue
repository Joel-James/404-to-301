<template>
    <div class="alignleft actions bulkactions" v-if="hasOptions">
        <label :for="actionId" class="screen-reader-text">{{ actionLabel }}</label>
        <select name="action" :id="actionId" v-model="bulkAction">
            <option value="-1">{{ actionLabel }}</option>
            <option v-for="option in actionOptions" :value="option.key">{{ option.label }}</option>
        </select>
        <button type="button" class="button" @click="actionClickHandler">{{ actionSubmit }}</button>
    </div>
</template>

<script>

	export default {
		name: 'BulkAction',

		props: {
			actionKey: {
				type: String,
				required: true,
			},
			actionLabel: {
				type: String,
				required: true
			},
			actionOptions: {
				type: Array,
				required: true
			},
			actionSubmit: {
				type: String,
				required: false,
				default: 'Submit'
			},
			actionClick: {
				type: Boolean,
				default: true
			},
			isTop: {
				type: Boolean,
				default: true
			},
		},

		computed: {
			hasOptions() {
				return this.actionOptions.length > 0;
			},

			actionId() {
				return this.actionKey + this.position;
			}
		},

		data() {
			return {
				bulkAction: -1,
				position: this.isTop ? 'top' : 'bottom',
			}
		},

		methods: {
			actionClickHandler( action ) {
				this.$root.$emit( 'listTableBulkActionSubmit', {
					action: this.actionKey,
					selected: this.bulkAction,
					position: this.position,
				} );
			}
		}
	};
</script>

