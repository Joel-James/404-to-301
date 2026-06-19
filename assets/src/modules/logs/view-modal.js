import { __, sprintf } from '@wordpress/i18n'
import { dateI18n } from '@wordpress/date'
import { Button, ExternalLink } from '@wordpress/components'

/**
 * Read-only "View details" modal for a single log row.
 *
 * Triggered from the row's ⋮ menu. Renders every field on the row in
 * a definition-list layout. Unlike the cells in the DataViews table,
 * values here are NOT truncated — long URLs / User-Agents wrap on to
 * multiple lines so the operator can see the whole string.
 *
 * When the log is linked to a custom redirect (`redirect_id` is set on
 * the row), a status note is shown calling that out so the operator
 * doesn't wonder why the same path stopped re-appearing.
 *
 * @param {Object}   props
 * @param {Object[]} props.items      Selected rows. DataViews always
 *                                    passes an array; we render the
 *                                    first row for this single-item
 *                                    action (`supportsBulk: false`).
 * @param {Function} props.closeModal Provided by DataViews.
 */
const ViewDetails = ({ items, closeModal }) => {
	const log = items?.[0]

	if (!log) {
		return null
	}

	const statusLabel = log.status_label || ''
	const hasRedirect = log.redirect_id != null && log.redirect_id > 0

	const formatDate = (value) =>
		value ? dateI18n('M j, Y g:i a', value) : '—'

	// Mirror the OVERRIDE_* enum from the server. Empty / 0 means
	// "follow the global setting" and reads as "Global" here.
	const overrideLabel = (value) => {
		switch (parseInt(value, 10)) {
			case 1:
				return __('Enabled', '404-to-301')
			case 2:
				return __('Disabled', '404-to-301')
			default:
				return __('Global', '404-to-301')
		}
	}

	// DataViews wraps `RenderModal` content in its own <Modal> already
	// (see @wordpress/dataviews ActionModal). Returning a fragment here
	// avoids the nested-modal blank overlay.
	return (
		<div className="d404-log-details">
			{hasRedirect && (
				<p className="d404-log-details__notice">
					{__(
						'A custom redirect is set for this path. Future visits will be redirected automatically.',
						'404-to-301',
					)}
				</p>
			)}

			<dl className="d404-log-details__list">
				<dt>{__('404 path', '404-to-301')}</dt>
				<dd className="d404-log-details__mono">{log.url || '—'}</dd>

				<dt>{__('Referrer', '404-to-301')}</dt>
				<dd>
					{log.ref ? (
						<ExternalLink href={log.ref}>{log.ref}</ExternalLink>
					) : (
						'—'
					)}
				</dd>

				<dt>{__('IP address', '404-to-301')}</dt>
				<dd>{log.ip || '—'}</dd>

				<dt>{__('User agent', '404-to-301')}</dt>
				<dd>{log.ua || '—'}</dd>

				<dt>{__('Method', '404-to-301')}</dt>
				<dd>{log.method || '—'}</dd>

				<dt>{__('Hits', '404-to-301')}</dt>
				<dd>{log.hits ?? 0}</dd>

				<dt>{__('Status', '404-to-301')}</dt>
				<dd>{statusLabel || '—'}</dd>

				<dt>{__('Custom redirect', '404-to-301')}</dt>
				<dd>
					{hasRedirect
						? sprintf(
								/* translators: %d: redirect id. */
								__('Linked redirect #%d', '404-to-301'),
								log.redirect_id,
						  )
						: __('Not set', '404-to-301')}
				</dd>

				<dt>{__('Override: Redirect', '404-to-301')}</dt>
				<dd>{overrideLabel(log.override_redirect)}</dd>

				<dt>{__('Override: Email', '404-to-301')}</dt>
				<dd>{overrideLabel(log.override_email)}</dd>

				<dt>{__('First seen', '404-to-301')}</dt>
				<dd>{formatDate(log.created_at)}</dd>

				<dt>{__('Last hit', '404-to-301')}</dt>
				<dd>{formatDate(log.updated_at)}</dd>
			</dl>

			<div className="d404-log-details__footer">
				<Button variant="tertiary" onClick={closeModal}>
					{__('Close', '404-to-301')}
				</Button>
			</div>
		</div>
	)
}

export default ViewDetails
