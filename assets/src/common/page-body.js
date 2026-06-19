import clsx from 'clsx'

/**
 * Page body wrapper.
 *
 * The default (`wide={false}`) gives the Settings-style 780px column.
 * `wide` pages (Logs, Redirects, Addons) opt-in to a responsive
 * container that grows up to ~1400px on big screens and tracks the
 * viewport on smaller ones.
 *
 * @param {Object}  props
 * @param {boolean} [props.wide=false] Use the wide responsive layout.
 * @param {Object}  props.children     Body content.
 */
const PageBody = ({ wide = false, children }) => (
	<div className={clsx('d404-page-body', { 'd404-page-body--wide': wide })}>
		{children}
	</div>
)

export default PageBody
