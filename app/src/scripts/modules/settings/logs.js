import Vue from 'vue'
import Logs from './tabs/logs'
import Fragment from 'vue-fragment'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

new Vue({
	render: (h) => h(Logs),
}).$mount('#dd-404-to-301-settings-logs')
