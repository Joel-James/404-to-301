import React from 'react'
import classNames from 'classnames'

export default function NavTabs(props) {
	let grids = []
	// We need to calculate grid size.
	Object.keys(props.navs).forEach(() => {
		grids.push('1fr')
	})

	return (
		<nav
			className="redirectpress-settings-tabs-wrapper"
			style={{
				gridTemplateColumns: grids.join(' '),
			}}
		>
			{Object.keys(props.navs).map((key) => (
				<a
					key={key}
					href="#"
					className={classNames({
						'redirectpress-settings-nav-tab': true,
						active: key === props.current,
					})}
					onClick={() => props.onChange(key)}
				>
					{props.navs[key]}
				</a>
			))}
		</nav>
	)
}
