<template>
    <tr>
        <th class="manage-column column-cb check-column">
            <input @change="selectAll" type="checkbox" value="1">
        </th>
        <th v-for="(value, key) in columns" :class="sortingClass(key)" :key="key" scope="col">
            <a @click.prevent="updateSorting(key)">
                <span>{{ value.label }}</span>
                <span class="sorting-indicator"></span>
            </a>
        </th>
    </tr>
</template>

<script>

	export default {
		name: 'TableTitleRow',

		props: {
			columns: {
				type: Object,
				required: true,
			},
		},

		computed: {
			sortingClass() {
				return ( key ) => {
					let sorted = this.sortKey === key;

					return {
						column: true,
						[ key ]: true,
						sortable: !sorted,
						sorted: sorted,
						[ this.sortOrder ]: true,
					}
				}
			},
		},

		data() {
			return {
				sortOrder: 'desc',
				sortKey: null,
			}
		},

		methods: {
			updateSorting( key ) {
				this.sortKey = key;

				// Interchange the order.
				if ( 'desc' === this.sortOrder ) {
					this.sortOrder = 'asc'
				} else {
					this.sortOrder = 'desc'
				}

				this.$emit( 'changeOrder', {
					order: this.sortOrder,
					key: this.sortKey,
				} );
			},

			selectAll( event ) {
				this.$emit( 'allSelect', {
					checked: event.target.checked
                } );
            }
		}
	};
</script>

