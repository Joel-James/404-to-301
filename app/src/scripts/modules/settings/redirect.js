import Vue from 'vue'
import Fragment from 'vue-fragment'
import Redirect from './tabs/redirect'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

new Vue({
	render: (h) => h(Redirect),
}).$mount('#dd-404-to-301-settings-redirect')
