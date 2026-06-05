/**
 * Small status pill used in the card header.
 *
 * Variants: `free` (grey), `premium` (blue), `success` (green —
 * for an activated license) and `warning` (amber — for an inactive
 * license on an installed addon).
 *
 * @param {Object} props
 * @param {string} [props.kind='default'] One of the variants above.
 * @param {Object} props.children          Badge label.
 */
const Badge = ({ children, kind = 'default' }) => (
	<span className={`d404-addon-badge d404-addon-badge--${kind}`}>
		{children}
	</span>
)

export default Badge
