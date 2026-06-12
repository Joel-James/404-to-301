/**
 * Reusable `Edit` components for `@wordpress/dataviews` DataForm fields.
 *
 * DataForm ships built-in form controls for `text`, `integer`, `select`,
 * `radio` and `datetime` — but it has no boolean toggle, no multiline
 * textarea, and its built-in `select` always prepends a placeholder
 * "Select item" row that breaks UX for fields that always carry a
 * defined value. The wrappers below fill those gaps:
 *
 *   - {@see ToggleEdit}      — `boolean` field rendered as a ToggleControl.
 *   - {@see TextareaEdit}    — multiline `text` field.
 *   - {@see EnumSelectEdit}  — `select` that omits the placeholder and
 *                              passes the field's `description` through
 *                              to SelectControl's `help` prop.
 *
 * Each component matches DataForm's Edit-component contract:
 *
 *     ({ data, field, onChange, hideLabelFromVision }) => JSX
 *
 *  - `data`               current form values
 *  - `field`              normalised field descriptor (incl. `getValue`)
 *  - `onChange(edits)`    partial-update callback — pass `{ [field.id]: v }`
 *  - `hideLabelFromVision` honoured for screen-reader-only labels
 */
import {
	SelectControl,
	TextareaControl,
	ToggleControl,
} from '@wordpress/components'
import PageSelect from './page-select'

/**
 * Boolean toggle. Coerces incoming values with `!!` so a stored `1` /
 * `0` from the REST layer renders correctly without extra mapping.
 */
export const ToggleEdit = ({ data, field, onChange, hideLabelFromVision }) => {
	const value = !!field.getValue({ item: data })
	return (
		<ToggleControl
			__nextHasNoMarginBottom
			label={field.label}
			help={field.description}
			hideLabelFromVision={hideLabelFromVision}
			checked={value}
			onChange={(v) => onChange({ [field.id]: v })}
		/>
	)
}

/**
 * Multiline text. Mirrors the built-in text control's prop shape so
 * it can be dropped in by setting `Edit: TextareaEdit` on a field.
 */
export const TextareaEdit = ({
	data,
	field,
	onChange,
	hideLabelFromVision,
}) => {
	const value = field.getValue({ item: data })
	return (
		<TextareaControl
			__nextHasNoMarginBottom
			label={field.label}
			help={field.description}
			hideLabelFromVision={hideLabelFromVision}
			value={value ?? ''}
			onChange={(v) => onChange({ [field.id]: v })}
		/>
	)
}

/**
 * Select that uses `field.elements` directly — no auto-inserted empty
 * placeholder, and `field.description` flows into SelectControl's `help`
 * so per-field hints survive the migration from the hand-rolled forms.
 *
 * For numeric enums (eg. `redirect_type` 301/302/307), `SelectControl`
 * stringifies the selected value — we route it back through `Number()`
 * when the underlying field type is `integer`.
 */
export const EnumSelectEdit = ({
	data,
	field,
	onChange,
	hideLabelFromVision,
}) => {
	const raw = field.getValue({ item: data })
	const isNumeric = field.type === 'integer'

	// SelectControl values are strings; normalise so the active option
	// is recognised when the field stores a number.
	const options = (field.elements ?? []).map((el) => ({
		value: String(el.value),
		label: el.label,
	}))

	return (
		<SelectControl
			__next40pxDefaultSize
			__nextHasNoMarginBottom
			label={field.label}
			help={field.description}
			hideLabelFromVision={hideLabelFromVision}
			value={raw === undefined || raw === null ? '' : String(raw)}
			options={options}
			onChange={(v) =>
				onChange({ [field.id]: isNumeric ? Number(v) : v })
			}
		/>
	)
}

/**
 * Searchable "existing page" picker for an `integer` page-ID field —
 * wraps {@see PageSelect} in the DataForm Edit contract so a field can
 * opt in with `Edit: PageSelectEdit`. Stores the chosen page ID, same
 * as the plain number input it replaces.
 */
export const PageSelectEdit = ({
	data,
	field,
	onChange,
	hideLabelFromVision,
}) => (
	<PageSelect
		label={field.label}
		help={field.description}
		hideLabelFromVision={hideLabelFromVision}
		value={field.getValue({ item: data })}
		onChange={(v) => onChange({ [field.id]: v })}
	/>
)
