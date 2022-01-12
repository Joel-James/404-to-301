<template>
	<div class="wp-filter">
		<ul class="filter-links">
			<tab-item
				v-for="nav in items"
				:key="nav.name"
				:active="activeTab"
				:name="nav.name"
				:label="nav.label"
				:icon="nav.icon"
				@tabClick="tabChange"
			/>
		</ul>
	</div>

	<div class="metabox-holder columns-2">
		<div class="postbox-container-1">
			<div id="main-sortables" class="meta-box-sortables ui-sortable">
				<div class="postbox ">

					<div class="inside">
						<router-view></router-view>
					</div>
				</div>
			</div>
		</div>
		<div class="postbox-container-2"></div>
	</div>
</template>

<script>
import TabItem from './tab-item'

export default {
	name: 'NavTabs',

	components: {TabItem},

	props: {
		items: Array,
		active: String,
	},

	data() {
		return {
			activeTab: this.active
		}
	},

	methods: {
		tabChange(tab) {
			this.activeTab = tab
		}
	}
}
</script>
