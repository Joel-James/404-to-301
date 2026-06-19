import { Component } from '@wordpress/element'
import { __ } from '@wordpress/i18n'
import { Notice } from '@wordpress/components'

/**
 * Catches render-time errors anywhere in the admin React tree and
 * shows an inline notice instead of letting the whole app unmount to a
 * blank page.
 *
 * Without this, a single undefined component — e.g. a
 * `@wordpress/components` `__experimental*` export that isn't present
 * in the WordPress-bundled version on a given site — throws during
 * render and React tears down the entire root, leaving a white screen.
 *
 * Error boundaries have to be class components: there is no hook
 * equivalent for `getDerivedStateFromError` / `componentDidCatch`.
 */
class ErrorBoundary extends Component {
	constructor(props) {
		super(props)
		this.state = { error: null }
	}

	static getDerivedStateFromError(error) {
		return { error }
	}

	componentDidCatch(error, info) {
		// Keep the full error + component stack in the console for
		// debugging; the UI only surfaces the message.
		// eslint-disable-next-line no-console
		console.error('404 to 301 admin error:', error, info)
	}

	render() {
		const { error } = this.state

		if (error) {
			return (
				<Notice status="error" isDismissible={false}>
					<p>
						<strong>
							{__(
								'Something went wrong while rendering this page.',
								'404-to-301',
							)}
						</strong>
					</p>
					<p>{error.message || String(error)}</p>
				</Notice>
			)
		}

		return this.props.children
	}
}

export default ErrorBoundary
