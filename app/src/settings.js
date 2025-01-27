import domReady from '@wordpress/dom-ready';
import { createRoot } from "@wordpress/element";

const SettingsPage = () => {
	return (
		<>
			<div>Hello</div>
		</>
	);
}

domReady( () => {
	const root = createRoot(
		document.getElementById( 'redirectpress-redirects-app' )
	);

	root.render( <SettingsPage /> );
} );