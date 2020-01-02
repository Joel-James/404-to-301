import Vue from 'vue'
import Router from 'vue-router'
import Logs from './../logs'

Vue.use( Router );

export default new Router( {
	routes: [
		{
			path: '/:page?/:group?',
			name: 'Logs',
			component: Logs
		},
	]
} )
