import clsx from 'clsx'

/**
 * Tab navigation for the settings page.
 *
 * @param {Object}   props          Component props.
 * @param {string}   props.current  Current tab key.
 * @param {Object}   props.navs     Map of tab key => label.
 * @param {Function} props.onChange Tab change handler.
 */
const TabNav = ({ current, navs, onChange }) => (
	<nav
		className="d404-tabs"
		style={{
			gridTemplateColumns: Object.keys(navs)
				.map(() => '1fr')
				.join(' '),
		}}
	>
		{Object.keys(navs).map((key) => (
			<a
				key={key}
				href={`#${key}`}
				className={clsx('d404-tab', { active: key === current })}
				onClick={(e) => {
					e.preventDefault()
					onChange(key)
				}}
			>
				{navs[key]}
			</a>
		))}
	</nav>
)

export default TabNav
