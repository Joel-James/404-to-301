import apiFetch from '@wordpress/api-fetch'

/**
 * Send API rest request using apiFetch.
 *
 * This is a wrapper function to include nonce and
 * our custom route base url.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
export function restRequest( options ) {
	apiFetch.use( apiFetch.createNonceMiddleware( window.dd404.rest_nonce ) );
	apiFetch.use( apiFetch.createRootURLMiddleware( window.dd404.rest_url ) );

	return apiFetch( options );
}
