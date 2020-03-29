<template>
    <p class="search-box">
        <label class="screen-reader-text"
               :for="id"
        >
            {{ label || $i18n.labels.search }}:
        </label>

        <input type="search"
               :id="id"
               v-model="search"
               @change="handleInput"
        />
        <button type="button"
                class="button"
                @click="doSearch"
        >
            {{ label }}
        </button>
    </p>
</template>

<script>

	export default {
		name: 'SearchBox',

		props: {
			id: {
				type: String,
				required: true
			},
			label: {
				type: String,
			},
			value: {
				type: String,
			}
		},

		data() {
			return {
				search: this.value,
			}
		},

		methods: {
			doSearch() {
				this.$emit( 'submitSearch', this.id );
			},

			handleInput() {
				this.$emit( 'input', this.search );
			}
		}
	};
</script>

