import Vue from 'vue'
import Fragment from 'vue-fragment'
//import General from './tabs/general'
import styles from './styles/main.scss'

Vue.config.productionTip = false

Vue.use(Fragment.Plugin)

var app3 = new Vue({
	el: '#app-3',
	data: {
		seen: true
	}
})
