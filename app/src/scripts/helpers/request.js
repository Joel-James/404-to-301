/* global dd4t3 */
const axios = require('axios')
const request = axios.create({
	baseURL: dd4t3.rest.base,
	headers: {
		'X-WP-Nonce': dd4t3.rest.nonce,
	},
})

export default request
