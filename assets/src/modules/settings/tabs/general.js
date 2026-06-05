import { __, sprintf, _n } from '@wordpress/i18n'
import { useEffect, useRef, useState } from '@wordpress/element'
import {
	Button,
	PanelBody,
	PanelRow,
	TextControl,
	ToggleControl,
} from '@wordpress/components'
import { closeSmall, plus } from '@wordpress/icons'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'

/**
 * Shallow-equal compare for two string arrays. Used to decide whether
 * an incoming `value` from the store represents an actual external
 * change (eg. settings reset) or is just an echo of our own onChange
 * roundtrip — in which case we leave local state alone so blank
 * editing rows aren't clobbered.
 */
const sameStrings = (a, b) =>
	a.length === b.length && a.every((v, i) => v === b[i])

/**
 * Build the initial local row list from the stored array. Always
 * includes at least one row so the field never renders empty.
 */
const buildRows = (value, nextId) => {
	const items = Array.isArray(value) ? value : []
	const seed = items.length ? items : ['']
	return seed.map((v) => ({ id: nextId(), value: v }))
}

/**
 * Repeater of single-line inputs for editing an array<string> setting.
 *
 * Why not a textarea or FormTokenField?
 *   - Textarea hides individual entries in a wall of text and makes
 *     long URLs wrap awkwardly mid-line.
 *   - FormTokenField truncates long URLs inside narrow chips and
 *     breaks the "scan a list" reading pattern.
 *
 * Each row owns a full-width TextControl plus a remove button, laid
 * out in a borderless table-like flex column. A trailing "Add path"
 * button appends a fresh blank row.
 *
 * Local state is the source of truth for the rendered rows so blank
 * editing rows can exist without persisting empty strings to the
 * store. We push the cleaned (non-empty, trimmed) array up on every
 * change; the upstream `value` is only resynced into local state when
 * it differs from what we last published — eg. when the user resets
 * settings elsewhere.
 */
const PathsRepeater = ({ value, onChange, placeholder }) => {
	// Monotonic id source for stable React keys across add/remove.
	const idRef = useRef(0)
	const nextId = () => {
		idRef.current += 1
		return idRef.current
	}

	const [rows, setRows] = useState(() => buildRows(value, nextId))

	// Resync from upstream only when it diverges from our published
	// view. Without this check, every onChange would round-trip back
	// here and wipe the blank trailing row the user is typing into.
	useEffect(() => {
		const published = rows
			.map((r) => r.value.trim())
			.filter(Boolean)
		const incoming = Array.isArray(value) ? value : []
		if (!sameStrings(published, incoming)) {
			setRows(buildRows(incoming, nextId))
		}
		// We intentionally omit `rows` from deps — the only signal
		// that should rebuild local state is an external value change.
		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [value])

	const publish = (next) => {
		setRows(next)
		onChange(next.map((r) => r.value.trim()).filter(Boolean))
	}

	const updateRow = (id, v) =>
		publish(rows.map((r) => (r.id === id ? { ...r, value: v } : r)))

	const removeRow = (id) => {
		// Never leave zero rows — collapsing to empty would force the
		// user to "Add path" before they could type anything again.
		const next = rows.filter((r) => r.id !== id)
		publish(next.length ? next : [{ id: nextId(), value: '' }])
	}

	const addRow = () => publish([...rows, { id: nextId(), value: '' }])

	const filledCount = rows.filter((r) => r.value.trim()).length

	// Disable "Add path" when the last row is empty — there's no
	// point appending another blank row if the existing one hasn't
	// been filled in. Acts as a soft form-validation cue without an
	// error message.
	const lastRow = rows[rows.length - 1]
	const canAddRow = !!lastRow && lastRow.value.trim().length > 0

	return (
		<div className="d404-repeater">
			<div
				className="d404-repeater__meta"
				aria-live="polite"
			>
				{sprintf(
					/* translators: %d: number of paths in the list. */
					_n('%d path', '%d paths', filledCount, '404-to-301'),
					filledCount,
				)}
			</div>

			<div className="d404-repeater__rows" role="list">
				{rows.map((row, index) => (
					<div
						key={row.id}
						className="d404-repeater__row"
						role="listitem"
					>
						<div className="d404-repeater__field">
							<TextControl
								__nextHasNoMarginBottom
								hideLabelFromVision
                size="compact"
								label={sprintf(
									/* translators: %d: row number. */
									__('Path %d', '404-to-301'),
									index + 1,
								)}
								value={row.value}
								onChange={(v) => updateRow(row.id, v)}
								placeholder={placeholder}
							/>
						</div>
						<div className="d404-repeater__actions">
							<Button
								size="small"
								isDestructive
								icon={closeSmall}
								label={__('Remove path', '404-to-301')}
								onClick={() => removeRow(row.id)}
							/>
						</div>
					</div>
				))}
			</div>

			<div className="d404-repeater__footer">
				<Button
					variant="secondary"
					size="small"
					icon={plus}
					onClick={addRow}
					disabled={!canAddRow}
					aria-disabled={!canAddRow}
					description={
						canAddRow
							? undefined
							: __(
								'Fill in the current row before adding another.',
								'404-to-301',
							)
					}
				>
					{__('Add path', '404-to-301')}
				</Button>
			</div>
		</div>
	)
}

const General = () => {
	const { getSetting, setSetting } = useSettings()

	/*
	 * Addon extension point. Addons hook into
	 * `d404.settings.general.fields` via `@wordpress/hooks` and return
	 * one or more React nodes rendered at the end of the General tab.
	 * The filter receives the `getSetting` / `setSetting` accessors so
	 * the injected controls read and write through the same hook the
	 * built-in fields use.
	 *
	 * Note: the hook name must start with a letter — `@wordpress/hooks`
	 * rejects names that lead with a digit, so we use the `d404` prefix
	 * here instead of `404_to_301`.
	 */
	const extra = applyFilters(
		'd404.settings.general.fields',
		null,
		{ getSetting, setSetting },
	)

	/*
	 * Cross-sell slot. No default promo today, but the filter exists so
	 * addons can inject (or replace) one without a future parent-side
	 * code change.
	 */
	const crossSell = applyFilters('d404.settings.general.cross_sell', null)

	return (
		<>
			<PanelBody title={__('Behaviour', '404-to-301')}>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Disable WordPress URL guessing',
							'404-to-301',
						)}
						help={__(
							'Stop WordPress from auto-correcting incorrect URLs to the closest matching post.',
							'404-to-301',
						)}
						checked={!!getSetting('disable_guessing', true)}
						onChange={(v) => setSetting('disable_guessing', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__(
							'Monitor post slug changes',
							'404-to-301',
						)}
						help={__(
							'Automatically create a redirect from the old URL to the new one when a post or page slug is renamed.',
							'404-to-301',
						)}
						checked={!!getSetting('monitor_post_slug', false)}
						onChange={(v) => setSetting('monitor_post_slug', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Mask IP addresses', '404-to-301')}
						help={__(
							'Drop visitor IPs before they hit the database. Useful for GDPR.',
							'404-to-301',
						)}
						checked={!!getSetting('mask_ip', false)}
						onChange={(v) => setSetting('mask_ip', v)}
					/>
				</PanelRow>
				<PanelRow>
					<ToggleControl
						__nextHasNoMarginBottom
						label={__('Track admin 404s', '404-to-301')}
						help={__(
							'Also process 404 requests that occur on the WordPress admin side. Off by default — most sites only care about front-end 404s.',
							'404-to-301',
						)}
						checked={!!getSetting('track_admin_404', false)}
						onChange={(v) => setSetting('track_admin_404', v)}
					/>
				</PanelRow>
			</PanelBody>

			<PanelBody title={__('Exclude paths', '404-to-301')}>
				<PanelRow>
					<div className="d404-repeater-field">
						<div className="d404-repeater-field__label">
							{__('Paths to ignore', '404-to-301')}
						</div>
						<PathsRepeater
							value={getSetting('exclude_paths', [])}
							onChange={(v) =>
								setSetting('exclude_paths', v)
							}
							placeholder={__(
								'e.g. /wp-json/ or /feed/',
								'404-to-301',
							)}
						/>
						<p className="components-base-control__help">
							{__(
								'Any 404 whose URL contains one of these substrings is skipped — no log, no redirect, no email.',
								'404-to-301',
							)}
						</p>
					</div>
				</PanelRow>
			</PanelBody>

			{extra}
			{crossSell}
		</>
	)
}

export default General
