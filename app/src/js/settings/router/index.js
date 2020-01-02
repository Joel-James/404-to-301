import Vue from 'vue'
import Router from 'vue-router'
import Email from './../tabs/email'
import General from './../tabs/general'

Vue.use( Router );

export default new Router( {
	linkActiveClass: 'nav-tab-active',
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
