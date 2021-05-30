import Vue from 'vue'
import Fragment from 'vue-fragment'
import General from './tabs/general'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

new Vue({
	render: (h) => h(General),
}).$mount('#dd-404-to-301-settings-general')
