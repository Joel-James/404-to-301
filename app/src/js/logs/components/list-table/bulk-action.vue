<template>
    <div class="alignleft actions bulkactions" v-if="hasOptions">
        <label :for="actionId" class="screen-reader-text">{{ actionLabel }}</label>
        <select name="action" :id="actionId" v-model="bulkAction" @change="actionChangeHandler">
            <option value="-1">{{ actionLabel }}</option>
            <option v-for="(label, option) in actionOptions" :value="option">{{ label }}</option>
        </select>
        <button v-if="enableSubmit" type="button" class="button" :disabled="disableSubmit" @click="actionClickHandler">{{ __( 'Apply', '404-to-301' ) }}</button>
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
				type: Object,
				required: true
			},
			isTop: {
				type: Boolean,
				default: true
			},
			enableSubmit: {
				type: Boolean,
				default: true
            }
		},

		computed: {
			hasOptions() {
				return Object.keys( this.actionOptions ).length > 0;
			},

			actionId() {
				return this.actionKey + this.position;
			},

            disableSubmit() {
				return -1 == this.bulkAction
            }
		},

		data() {
			return {
				bulkAction: -1,
				position: this.isTop ? 'top' : 'bottom',
			}
		},

		methods: {
			actionClickHandler() {
				this.$root.$emit( 'listTableBulkActionSubmit', {
					action: this.actionKey,
					selected: this.bulkAction,
					position: this.position,
				} );
			},

			actionChangeHandler() {
				this.$root.$emit( 'listTableBulkActionChange', {
					action: this.actionKey,
					selected: this.bulkAction,
					position: this.position,
				} );
            }
		}
	};
</script>

