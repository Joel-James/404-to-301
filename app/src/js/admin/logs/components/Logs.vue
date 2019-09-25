<template>
    <div id="poststuff">
        <div id="post-body">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post">
                        <NavTop />
                        <Table />
                        <NavBottom />
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import NavTop from './list-table/NavTop.vue'
	import NavBottom from './list-table/NavBottom.vue'
    import Table from './list-table/Table.vue'

	export default {

		name: 'Logs',

		components: {
			NavTop, NavBottom, Table
		},

		data() {
			return {
				message: 'Error logs logs will be here soon.'
			}
		}
	}
</script>
