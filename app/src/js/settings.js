/* global window, document */
if ( !window._babelPolyfill ) {
	require( '@babel/polyfill' );
}

import React from 'react';
import ReactDOM from 'react-dom';
import Settings from './containers/Settings.jsx';

/**
 * When DOM content is loaded, show our settings content.
 *
 * @since 4.0.0
 */
document.addEventListener( 'DOMContentLoaded', function () {
	ReactDOM.render(
		<Settings wpObject={ window.dd404_settings }/>,
		document.getElementById( 'dd404-settings' )
	);
} );
