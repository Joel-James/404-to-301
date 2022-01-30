import React from 'react'
import classNames from 'classnames'

export default (props) => {
	let grids = []
	// We need to calculate grid size.
	Object.keys(props.navs).forEach(() => {
		grids.push('1fr')
	})

	return (
		<nav
			className="dd4t3-tabs-wrapper"
			style={{
				gridTemplateColumns: grids.join(' ')
			}}
		>
			{Object.keys(props.navs).map((key) =>
				<a
					href="#"
					className={classNames({
						'dd4t3-nav-tab': true,
						active: key === props.current,
					})}
					onClick={() => props.onChange(key)}
				>
					{props.navs[key]}
				</a>
			)}
		</nav>
	);
}