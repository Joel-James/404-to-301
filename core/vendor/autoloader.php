<?php

/**
 * Thanks - Tom McFarlin
 * https://code.tutsplus.com/tutorials/using-namespaces-and-autoloading-in-wordpress-plugins-4--cms-27342
 */

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin by looking at the $class_name parameter being passed as an argument.
 *
 * The argument should be in the form: DuckDev\WP404\Namespace. The
 * function will then break the fully-qualified class name into its pieces and
 * will then build a file to the path based on the namespace.
 * The namespaces in this plugin map to the paths in the directory structure.
 *
 * @param string $class_name The fully-qualified name of the class to load.
 *
 * @since 4.0
 */
/** @noinspection PhpUnhandledExceptionInspection */
spl_autoload_register( function ( $class_name ) {
	// If the specified $class_name does not include our namespace, duck out.
	if ( false === strpos( $class_name, 'DuckDev\WP404' ) ) {
		return;
	}

	// Split the class name into an array to read the namespace and class.
	$file_parts = explode( '\\', $class_name );

	// Do a reverse loop through $file_parts to build the path to the file.
	$namespace = '';
	for ( $i = count( $file_parts ) - 1; $i > 1; $i -- ) {

		// Read the current component of the file part.
		$current = strtolower( $file_parts[ $i ] );
		$current = str_ireplace( '_', '-', $current );

		// If we're at the first entry, then we're at the filename.
		if ( count( $file_parts ) - 1 === $i ) {
			$file_name = "class-$current.php";
		} else {
			$namespace = '/' . $current . $namespace;
		}
	}

	// Now build a path to the file using mapping to the file location.
	$filepath = trailingslashit( plugin_dir_path( DD404_PLUGIN_FILE ) . 'core' . $namespace );

	// Make sure we don't break.
	if ( ! empty( $file_name ) ) {
		$filepath .= $file_name;
	}

	// If the file exists in the specified path, then include it.
	if ( file_exists( $filepath ) ) {
		/** @noinspection PhpIncludeInspection */
		include_once $filepath;
	} else {
		wp_die( printf( __( 'The file attempting to be loaded at %s does not exist.', '404-to-301' ), $filepath ) );
	}
} );