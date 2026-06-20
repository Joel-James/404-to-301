/**
 * Summary card strip — a row of stat cards shown above a DataViews list.
 *
 * @param {Object}   props
 * @param {Array}    props.cards     Array of { label, value } objects.
 * @param {boolean}  props.isLoading Whether data is still being fetched.
 */
const SummaryCards = ({ cards, isLoading }) => (
	<div className={`d404-summary${isLoading ? ' d404-summary--loading' : ''}`}>
		{cards.map(({ label, value }) => (
			<div key={label} className="d404-summary__card">
				<span className="d404-summary__value">
					{isLoading ? '—' : value.toLocaleString()}
				</span>
				<span className="d404-summary__label">{label}</span>
			</div>
		))}
	</div>
)

export default SummaryCards
