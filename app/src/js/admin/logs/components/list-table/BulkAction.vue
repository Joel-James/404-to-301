<template>
    <div class="alignleft actions bulkactions" v-if="hasOptions">
        <label :for="actionId" class="screen-reader-text">{{ actionLabel }}</label>
        <select name="action" :id="actionId" v-model="bulkAction">
            <option value="-1">{{ actionLabel }}</option>
            <option v-for="option in actionOptions" :value="option.key">{{ option.label }}</option>
        </select>
        <button type="button" :id="actionId + '-submit'" class="button">{{ actionSubmit }}</button>
    </div>
</template>

<script>

	export default {

		/**
		 * Current component name.
		 *
		 * @since 4.0.0
		 */
		name: 'BulkAction',

		/**
		 * Define properties of this component.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
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
			isTop: {
				type: Boolean,
				default: true
			},
		},

		/**
		 * Dynamic methods to handle table.
		 *
		 * @since 4.0.0
		 *
		 * @returns {object}
		 */
		computed: {
			/**
			 * Is there bulk actions available.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			hasOptions() {
				return this.actionOptions.length > 0;
			},

			/**
			 * Is there bulk actions available.
			 *
			 * @since 4.0.0
			 *
			 * @returns {object}
			 */
			actionId() {
				if ( this.isTop ) {
					return this.actionKey;
				} else {
					return this.actionKey + '-bottom';
				}
			}
		},

		data() {
			return {
				bulkAction: -1,
			}
		}
	};
</script>

