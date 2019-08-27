/* global window, document */
if (!window._babelPolyfill) {
	require('@babel/polyfill');
}

import React from 'react';
import ReactDOM from 'react-dom';
import Settings from './containers/Settings.jsx';

document.addEventListener('DOMContentLoaded', function () {
	ReactDOM.render(<Settings wpObject={window.dd404_settings}/>, document.getElementById('dd404-settings'));
});
