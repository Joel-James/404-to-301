import { __, sprintf, _n } from '@wordpress/i18n'
import { useEffect, useRef, useState } from '@wordpress/element'
import { Button, TextControl } from '@wordpress/components'
import { closeSmall, plus } from '@wordpress/icons'

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
 *
 * @param {Object}   props
 * @param {string[]} props.value       Current stored list.
 * @param {Function} props.onChange    Called with the cleaned array on every edit.
 * @param {string}   props.placeholder Per-row input placeholder.
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

export default PathsRepeater
