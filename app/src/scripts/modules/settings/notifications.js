import Vue from 'vue'
import Notifications from './tabs/notifications'
import Fragment from 'vue-fragment'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

new Vue({
	render: (h) => h(Notifications),
}).$mount('#dd-404-to-301-settings-notifications')
