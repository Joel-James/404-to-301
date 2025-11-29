/* global duckdev404 */
const SettingsHeader = ( { title, children } ) => {
	return (
		<div className="duckdev-404-settings-header">
			<div className="duckdev-404-settings-title-section">
				<h1>{ title }</h1>
				<abbr
					title={ 'Version: ' + duckdev404.version }
					className="version"
				>
					{ duckdev404.version }
				</abbr>
			</div>
			{ children }
		</div>
	)
}

export default SettingsHeader
