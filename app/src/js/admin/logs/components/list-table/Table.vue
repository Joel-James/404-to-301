<template>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <HeaderRow/>
        </thead>
        <tbody>
            <Row/>
            <Row/>
        </tbody>
    </table>
</template>

<script>
	import HeaderRow from './HeaderRow.vue'
	import Row from './Row.vue'

	export default {

		name: 'Table',

		components: {
			HeaderRow, Row
		},

		data() {
			return {};
		}
	};
</script>