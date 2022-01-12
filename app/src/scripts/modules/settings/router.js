import {createRouter, createWebHashHistory} from 'vue-router'
import TabRedirect from './tabs/tab-redirect'
import TabEmail from './tabs/tab-email'
import TabGeneral from './tabs/tab-general'
import TabLogs from './tabs/tab-logs'
import TabInfo from './tabs/tab-info'

export default createRouter({
	history: createWebHashHistory(),
	linkActiveClass: 'current',
	routes: [
		{
			path: '/',
			redirect: '/redirect',
		},
		{
			path: '/redirect',
			component: TabRedirect
		},
		{
			path: '/logs',
			component: TabLogs
		},
		{
			path: '/email',
			component: TabEmail
		},
		{
			path: '/general',
			component: TabGeneral
		},
		{
			path: '/info',
			component: TabInfo
		},
	]
})
