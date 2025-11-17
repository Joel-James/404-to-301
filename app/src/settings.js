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
		document.getElementById( '404-to-301-redirects-app' )
	);

	root.render( <SettingsPage /> );
} );