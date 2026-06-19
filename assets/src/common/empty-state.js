import { Icon } from '@wordpress/icons'

/**
 * Empty-state panel for the DataViews tables.
 *
 * DataViews renders its own hardcoded "No results" line and offers no
 * prop to override it, so the lists hide that default (see the
 * `dataviews-no-results` rule in the page styles) and render this in its
 * place — sat in the table body area so it reads like a single, full
 * width row rather than a bare sentence.
 *
 * @param {Object}      props
 * @param {Object}      props.icon          A `@wordpress/icons` icon.
 * @param {string}      props.title         Headline (eg. "No redirects yet").
 * @param {string}      [props.description] Supporting line under the title.
 * @param {JSX.Element} [props.action]      Optional CTA (button/link).
 * @return {JSX.Element}
 */
const EmptyState = ({ icon, title, description, action }) => (
	<div className="d404-empty-state" role="status">
		<div className="d404-empty-state__inner">
			{icon && (
				<span className="d404-empty-state__icon" aria-hidden="true">
					<Icon icon={icon} size={28} />
				</span>
			)}
			<div className="d404-empty-state__text">
				<p className="d404-empty-state__title">{title}</p>
				{description && (
					<p className="d404-empty-state__description">
						{description}
					</p>
				)}
			</div>
			{action && <div className="d404-empty-state__action">{action}</div>}
		</div>
	</div>
)

export default EmptyState
