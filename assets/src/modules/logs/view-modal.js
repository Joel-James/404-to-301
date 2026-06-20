import { __, sprintf } from '@wordpress/i18n'
import { dateI18n } from '@wordpress/date'
import { Button, ExternalLink } from '@wordpress/components'

/**
 * Read-only "View details" modal for a single log row.
 *
 * Triggered from the row's ⋮ menu. Groups every field into three
 * labelled table sections — Request, Visit info, Settings — with the
 * 404 path displayed prominently above them in a <code> block.
 *
 * @param {Object}   props
 * @param {Object[]} props.items      Selected rows (single-item action).
 * @param {Function} props.closeModal Provided by DataViews.
 */
const ViewDetails = ({ items, closeModal }) => {
	const log = items?.[0]

	if (!log) {
		return null
	}

	const hasRedirect = log.redirect_id != null && log.redirect_id > 0

	const formatDate = (value) =>
		value ? dateI18n('M j, Y g:i a', value) : '—'

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

	const statusSlug = ['open', 'ignored', 'fixed', 'custom'][log.status] ?? 'open'
	const statusLabel = log.status_label || statusSlug

	return (
		<div className="d404-log-details">
			{/* Prominent 404 path header — outside the table so it reads as
			    the subject of this modal, not just another row in the data. */}
			<div className="d404-log-details__header">
				<code className="d404-log-details__path">{log.url || '—'}</code>
				<span className={`d404-log-details__status d404-log-details__status--${statusSlug}`}>
					{statusLabel}
				</span>
			</div>

			{hasRedirect && (
				<p className="d404-log-details__notice">
					{__(
						'A custom redirect is set for this path. Future visits will be redirected automatically.',
						'404-to-301',
					)}
				</p>
			)}

			<div className="d404-log-details__section">
				<h3 className="d404-log-details__section-title">
					{__('Request', '404-to-301')}
				</h3>
				<table className="d404-log-details__table">
					<tbody>
						<tr>
							<th>{__('Referrer', '404-to-301')}</th>
							<td>
								{log.ref ? (
									<ExternalLink href={log.ref}>{log.ref}</ExternalLink>
								) : (
									'—'
								)}
							</td>
						</tr>
						<tr>
							<th>{__('IP address', '404-to-301')}</th>
							<td className="d404-log-details__mono">{log.ip || '—'}</td>
						</tr>
						<tr>
							<th>{__('User agent', '404-to-301')}</th>
							<td className="d404-log-details__mono">{log.ua || '—'}</td>
						</tr>
						<tr>
							<th>{__('Method', '404-to-301')}</th>
							<td>{log.method || '—'}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div className="d404-log-details__section">
				<h3 className="d404-log-details__section-title">
					{__('Visit info', '404-to-301')}
				</h3>
				<table className="d404-log-details__table">
					<tbody>
						<tr>
							<th>{__('Hits', '404-to-301')}</th>
							<td>{(log.hits ?? 0).toLocaleString()}</td>
						</tr>
						<tr>
							<th>{__('First seen', '404-to-301')}</th>
							<td>{formatDate(log.created_at)}</td>
						</tr>
						<tr>
							<th>{__('Last hit', '404-to-301')}</th>
							<td>{formatDate(log.updated_at)}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div className="d404-log-details__section">
				<h3 className="d404-log-details__section-title">
					{__('Settings', '404-to-301')}
				</h3>
				<table className="d404-log-details__table">
					<tbody>
						<tr>
							<th>{__('Custom redirect', '404-to-301')}</th>
							<td>
								{hasRedirect
									? sprintf(
											/* translators: %d: redirect id. */
											__('Linked to redirect #%d', '404-to-301'),
											log.redirect_id,
									  )
									: __('Not set', '404-to-301')}
							</td>
						</tr>
						<tr>
							<th>{__('Redirect override', '404-to-301')}</th>
							<td>{overrideLabel(log.override_redirect)}</td>
						</tr>
						<tr>
							<th>{__('Email override', '404-to-301')}</th>
							<td>{overrideLabel(log.override_email)}</td>
						</tr>
					</tbody>
				</table>
			</div>

			<div className="d404-log-details__footer">
				<Button variant="tertiary" onClick={closeModal}>
					{__('Close', '404-to-301')}
				</Button>
			</div>
		</div>
	)
}

export default ViewDetails
