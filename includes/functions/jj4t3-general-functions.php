<?php

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Get plugin setting value.
 *
 * Handle the exceptional case properly
 * and return false.
 *
 * @param mixed $option  Option name.
 * @param mixed $default Default value if not exist.
 *
 * @since  3.0.0
 * @access public
 *
 * @return string|array
 */
function jj4t3_get_option( $option = false, $default = false ) {

	if ( ! $option ) {
		return $default;
	}

	// Get our plugin settings value.
	$settings = (array) get_option( 'i4t3_gnrl_options', array() );

	// Return false, if not exist.
	if ( empty( $settings[ $option ] ) ) {
		return $default;
	}

	return $settings[ $option ];
}

/**
 * Update a single setting value.
 *
 * This helper function is used to update a single
 * setting value of our plugin settings array.
 * Default WordPress update_option() function can
 * update the the array only.
 *
 * @param string $option Option name.
 * @param mixed  $value  Value to update.
 *
 * @since  3.0.0
 * @access public
 *
 * @return void
 */
function jj4t3_update_option( $option, $value = '' ) {

	$settings = (array) get_option( 'i4t3_gnrl_options', array() );

	$settings[ $option ] = $value;

	update_option( 'i4t3_gnrl_options', $settings );
}

/**
 * Check if the redirect for 404 enabled.
 *
 * Check if the user selected "No Redirect" option
 * for redirect.
 * Registering filter - "jj4t3_redirect_enabled".
 *
 * @since  3.0.0
 * @access public
 *
 * @return boolean
 */
function jj4t3_redirect_enabled() {

	// Get redirect to option value.
	$enabled = jj4t3_redirect_to();

	/**
	 * Filter hook to alter redirect option.
	 *
	 * Return boolean to enable/disable redirect.
	 *
	 * @since 2.0.0
	 */
	return (bool) apply_filters( 'jj4t3_redirect_enabled', $enabled );
}

/**
 * Check if the email notification is enabled.
 *
 * Registering filter - "jj4t3_email_notify_enabled".
 *
 * @since  3.0.0
 * @access public
 *
 * @return boolean
 */
function jj4t3_email_notify_enabled() {

	// Get email notification option.
	$enabled = jj4t3_get_option( 'email_notify' );

	/**
	 * Filter hook to alter email notification option.
	 *
	 * Return boolean to enable/disable. For whatever value you return
	 * it will consider it's boolean only.
	 *
	 * @since 2.0.0
	 */
	return (bool) apply_filters( 'jj4t3_email_notify_enabled', $enabled );
}

/**
 * Check if the error logging is enabled.
 *
 * Registering filter - "jj4t3_log_enabled".
 *
 * @since  3.0.0
 * @access public
 *
 * @return boolean
 */
function jj4t3_log_enabled() {

	// Get error logging option.
	$enabled = jj4t3_get_option( 'redirect_log' );

	/**
	 * Filter hook to alter error logging.
	 *
	 * Return boolean to enable/disable logging.For whatever value you return
	 * it will consider it's boolean only.
	 *
	 * @since 2.0.0
	 */
	return (bool) apply_filters( 'jj4t3_log_enabled', $enabled );
}

/**
 * Get enabled redirect to.
 *
 * Retured type of redirect target.
 * If redirect is not enabled, return false.
 * If unknown value is given return false.
 *
 * @since  3.0.0
 * @access public
 *
 * @return string|boolean
 */
function jj4t3_redirect_to() {

	/**
	 * Filter hook to get redirect to option value.
	 *
	 * Accepts only 2 values - page, link.
	 * If any other value is returned, it will considered
	 * as the redirect is disabled.
	 *
	 * @since 2.0.0
	 */
	$to = apply_filters( 'jj4t3_redirect_to', jj4t3_get_option( 'redirect_to' ) );

	// Verify that only allowed values accepted.
	if ( in_array( $to, array( 'page', 'link' ) ) ) {
		return $to;
	}

	return false;
}

/**
 * Get redirect type.
 *
 * This function is used to get the redirect
 * status code selected by the user.
 * Registering filter - jj4t3_redirect_type
 * to alter redirect status code.
 *
 * @since  3.0.0
 * @access private
 *
 * @return int Redirect status code.
 */
function jj4t3_redirect_type() {

	$type = (int) jj4t3_get_option( 'redirect_type' );

	/**
	 * Filter to modify currently set redirect type.
	 *
	 * Return only valid HTTP status codes.
	 * If you are returning custom status codes other than the default
	 * values, please make sure that you have added that to "jj4t3_redirect_statuses"
	 * filter first. Otherwise it will be ignored.
	 *
	 * @since 2.0.0
	 */
	$status = apply_filters( 'jj4t3_redirect_type', $type );

	// Verify that redirect status is allowed.
	if ( in_array( $status, array_keys( jj4t3_redirect_statuses() ) ) ) {
		return $status;
	}

	return 301;
}

/**
 * Check if the current user is real human.
 *
 * This function is used to check the current
 * visitor is bot or real human based on the
 * browser.
 * If it is a bot, browser variables may not
 * be there.
 * DO NOT relay on this function for serious actions
 * as it may be wrong in some cases.
 *
 * @since  3.0.0
 * @access private
 *
 * @global bool $is_gecko
 * @global bool $is_opera
 * @global bool $is_safari
 * @global bool $is_chrome
 * @global bool $is_IE
 * @global bool $is_edge
 * @global bool $is_NS4
 * @global bool $is_lynx
 *
 * @return boolean
 */
function jj4t3_is_human() {

	// If mobile OS is found it real user.
	if ( wp_is_mobile() ) {

		/**
		 * Filter to modify human vs bot checking.
		 *
		 * If you want to add additional conditions to the human checking
		 * function, use this filter and return a boolean.
		 * For whatever value you return it will consider it's boolean only.
		 *
		 * @param boolean is real human.
		 * @param string  device type.
		 *
		 * @since 3.0.0
		 */
		return (bool) apply_filters( 'jj4t3_is_human', true, 'mobile' );
	}

	// WordPress global variables for browsers.
	global $is_gecko, $is_opera, $is_safari, $is_chrome, $is_IE, $is_edge, $is_NS4, $is_lynx;

	$human = ( $is_gecko || $is_opera || $is_safari || $is_chrome || $is_IE || $is_edge || $is_NS4 || $is_lynx );

	/**
	 * This filter is documented above.
	 */
	return (bool) apply_filters( 'jj4t3_is_human', $human, 'desktop' );
}

/**
 * Set allowed status codes to redirect.
 *
 * Currently we are using only 3 status codes.
 * You can modify this to use more status using the filter.
 * Registering filter - "jj4t3_redirect_statuses".
 *
 * @link   https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html HTTP status codes.
 * @since  3.0.0
 * @access private
 *
 * @return array Allowed HTTP status codes.
 */
function jj4t3_redirect_statuses() {

	$statuses = array(
		301 => __( '301 Redirect (SEO)', '404-to-301' ),
		302 => __( '302 Redirect', '404-to-301' ),
		307 => __( '307 Redirect', '404-to-301' ),
	);

	/**
	 * Filter for allowed status codes.
	 *
	 * If you want to add additional HTTP status codes
	 * for redirect, please use this filter and add to
	 * the statuses array.
	 * DO NOT remove default values (301, 302 and 307) from
	 * the array.
	 *
	 * @since 3.0.0
	 */
	return (array) apply_filters( 'jj4t3_redirect_statuses', $statuses );
}

/**
 * Available columns in error logs table.
 *
 * This columns are being used few times. Use this to avoid
 * unwanted names.
 * Registering filter - "jj4t3_redirect_statuses".
 *
 * @since  3.0.0
 * @access private
 *
 * @return array Allowed HTTP status codes.
 */
function jj4t3_log_columns() {

	$columns = array(
		'date'     => __( 'Date', '404-to-301' ),
		'url'      => __( '404 Path', '404-to-301' ),
		'ref'      => __( 'From', '404-to-301' ),
		'ip'       => __( 'IP Address', '404-to-301' ),
		'ua'       => __( 'User Agent', '404-to-301' ),
		'redirect' => __( 'Redirect', '404-to-301' ),
	);

	/**
	 * Filter for available columns.
	 *
	 * These are the availble column names in 404
	 * error logs.
	 * Registering filter - "jj4t3_log_columns".
	 *
	 * @param array columns name and slug.
	 *
	 * @since 3.0.0
	 */
	return (array) apply_filters( 'jj4t3_log_columns', $columns );
}

/**
 * Retrive value from $_REQUEST.
 *
 * Helper function to retrive data from $_REQUEST
 * We can use this function to get values from request
 * and get a default value if the current key does not exist
 * or empty.
 * Output will be trimmed.
 *
 * @param string $key     Key to get from request.
 * @param mixed  $default Default value.
 *
 * @since  3.0.0
 * @access public
 *
 * @return array|string
 */
function jj4t3_from_request( $key = '', $default = '' ) {
	// Return default value if key is not given.
	if ( empty( $key ) || ! is_string( $key ) ) {
		return $default;
	}

	// Return default value if key not set.
	if ( ! isset( $_REQUEST[ $key ] ) ) {
		return $default;
	}

	// Trim output.
	if ( is_string( $_REQUEST[ $key ] ) ) {
		return sanitize_text_field( $_REQUEST[ $key ] );
	} elseif ( is_array( $_REQUEST[ $key ] ) ) {
		return array_map( 'sanitize_text_field', $_REQUEST[ $key ] );
	}

	return $default;
}
