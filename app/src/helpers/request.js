/* global redirectpress */
const axios = require('axios')
const request = axios.create({
	baseURL: redirectpress.rest.base,
	headers: {
		'X-WP-Nonce': redirectpress.rest.nonce,
	},
})

export default request
