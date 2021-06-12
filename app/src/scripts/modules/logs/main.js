import Vue from 'vue'
import App from './app'
import Fragment from 'vue-fragment'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

new Vue({
	render: (h) => h(App),
}).$mount('#dd-404-to-301-logs')
