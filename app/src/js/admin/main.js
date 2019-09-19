import Vue from 'vue'
import App from './App.vue'
import router from './router'

Vue.config.productionTip = false

/* eslint-disable no-new */
new Vue({
    el: '#dd404-settings-app',
    router,
    render: h => h(App)
});
