/* global d404 */

/**
 * Settings page header with title, version and tab navigation.
 *
 * @param {Object} props          Component props.
 * @param {string} props.title    Page title.
 * @param {Object} props.children Tab navigation.
 */
const PageHeader = ({ title, children }) => {
	const version = (typeof d404 !== 'undefined' && d404.version) || ''

	return (
		<div className="d404-page-header">
			<div className="d404-page-title">
				<h1>{title}</h1>
				{version && (
					<abbr className="d404-version" title={`Version: ${version}`}>
						{version}
					</abbr>
				)}
			</div>
			{children}
		</div>
	)
}

export default PageHeader
