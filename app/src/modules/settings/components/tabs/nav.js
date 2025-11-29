import classNames from 'classnames'

const TabNav = ( { current, navs, onChange } ) => {
	const grids = []
	// We need to calculate grid size.
	Object.keys( navs ).forEach( () => {
		grids.push( '1fr' )
	} )

	return (
		<nav
			className="duckdev-404-settings-tabs-wrapper"
			style={ {
				gridTemplateColumns: grids.join( ' ' ),
			} }
		>
			{ Object.keys( navs ).map( ( key ) => (
				<a
					key={ key }
					href={ '#' + key }
					className={ classNames( {
						'duckdev-404-settings-nav-tab': true,
						active: key === current,
					} ) }
					onClick={ () => onChange( key ) }
				>
					{ navs[ key ] }
				</a>
			) ) }
		</nav>
	)
}

export default TabNav
