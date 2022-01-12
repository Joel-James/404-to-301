<template>
	<div class="nav-tab-wrapper">
		<tab-item
			v-for="nav in items"
			:key="nav.name"
			:active="activeTab"
			:name="nav.name"
			:label="nav.label"
			:icon="nav.icon"
			@tabClick="tabChange"
		/>
	</div>
	<div class="tabs-content">

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
