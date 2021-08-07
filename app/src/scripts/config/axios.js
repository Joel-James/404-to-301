/* global dd4t3 */
export const axiosConfig = {
	baseURL: dd4t3.rest.base,
	headers: {
		'X-WP-Nonce': dd4t3.rest.nonce,
	},
};
