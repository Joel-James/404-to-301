import Vue from 'vue'
import Router from 'vue-router'
import General from 'admin/settings/components/General.vue'
import Email from 'admin/settings/components/Email.vue'

Vue.use( Router );

export default new Router( {
	routes: [
		{
			path: '/',
			name: 'General',
			component: General
		},
		{
			path: '/email',
			name: 'Email',
			component: Email
		},
	]
} )
