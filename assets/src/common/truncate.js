/**
 * Inline text node that visually truncates with an ellipsis and exposes
 * the full value as a native tooltip on hover.
 *
 * @param {Object} props
 * @param {string} props.value     Full text content (used for the tooltip).
 * @param {string} [props.className] Optional extra class for styling hooks.
 * @param {*}      [props.children]  Optional render override; defaults to value.
 * @return {JSX.Element|null}
 */
const Truncate = ({ value, className = '', children }) => {
	if (value === null || value === undefined || value === '') {
		return null
	}

	return (
		<span
			className={`d404-truncate ${className}`.trim()}
			title={value}
		>
			{children ?? value}
		</span>
	)
}

export default Truncate
