import apiFetch from '@wordpress/api-fetch'

/**
 * Send API rest GET request using apiFetch.
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
export function restGet( options ) {
	options = options || {};

	options.method = 'GET';

	apiFetch.use( apiFetch.createNonceMiddleware( window.dd404.rest_nonce ) );
	apiFetch.use( apiFetch.createRootURLMiddleware( window.dd404.rest_url ) );

	// Add param support.
	if ( options.params ) {
		const urlParams = new URLSearchParams(
			Object.entries( options.params )
		);

		options.path = options.path + '?' + urlParams;
	}

	return apiFetch( options );
}

/**
 * Send API rest POST request using apiFetch.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
export function restPost( options ) {
	options = options || {};

	options.method = 'POST';

	apiFetch.use( apiFetch.createNonceMiddleware( window.dd404.rest_nonce ) );
	apiFetch.use( apiFetch.createRootURLMiddleware( window.dd404.rest_url ) );

	return apiFetch( options );
}

/**
 * Send API rest DELETE request using apiFetch.
 *
 * @param {object} options apiFetch options.
 *
 * @since 4.0.0
 *
 * @return {string}
 **/
export function restDelete( options ) {
	options = options || {};

	options.method = 'DELETE';

	apiFetch.use( apiFetch.createNonceMiddleware( window.dd404.rest_nonce ) );
	apiFetch.use( apiFetch.createRootURLMiddleware( window.dd404.rest_url ) );

	return apiFetch( options );
}
