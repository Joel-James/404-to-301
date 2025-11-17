/* global duckdevFourNotFour */
const axios = require('axios')
const request = axios.create({
	baseURL: duckdevFourNotFour.rest.base,
	headers: {
		'X-WP-Nonce': duckdevFourNotFour.rest.nonce,
	},
})

export default request
