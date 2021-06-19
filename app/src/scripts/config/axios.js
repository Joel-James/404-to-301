/* global dd404 */
export const axiosConfig = {
	baseURL: dd404.rest.base,
	headers: {
		'X-WP-Nonce': dd404.rest.nonce,
	},
};
