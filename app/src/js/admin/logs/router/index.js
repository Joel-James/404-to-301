import Vue from 'vue'
import Router from 'vue-router'
import Logs from 'admin/logs/components/Logs.vue'
import Settings from 'admin/logs/components/Settings.vue'

Vue.use( Router );

export default new Router( {
	routes: [
		{
			path: '/:page?/:group?',
			name: 'Logs',
			component: Logs
		},
		{
			path: '/settings',
			name: 'Settings',
			component: Settings
		},
	]
} )
