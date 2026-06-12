import { __, sprintf } from '@wordpress/i18n'
import { ComboboxControl } from '@wordpress/components'
import { useMemo, useState } from '@wordpress/element'
import { useSelect } from '@wordpress/data'
import { useDebounce } from '@wordpress/compose'
import { store as coreStore } from '@wordpress/core-data'
import { decodeEntities } from '@wordpress/html-entities'

// Only request the two fields we render, and cap the result set — the
// control is a type-to-search box, not a full page dump, so a page of
// matches is plenty and keeps the request light on large sites.
const QUERY_FIELDS = 'id,title'
const PER_PAGE = 20

const pageLabel = (page) =>
	page
		? decodeEntities(page.title?.rendered) ||
		  // eslint-disable-next-line @wordpress/i18n-translator-comments
		  sprintf(__('Page #%d', '404-to-301'), page.id)
		: ''

/**
 * Searchable "existing page" picker.
 *
 * Replaces the bare numeric page-ID input with an async ComboboxControl:
 * the user types a page title, we query the core-data `page` entity
 * (debounced, capped at {@link PER_PAGE}) and resolve the chosen title —
 * but still store the page **ID** in `redirect_page`, so the saved value
 * and the server-side resolver are unchanged. Scales to any number of
 * pages because only the matches for the typed query are ever fetched.
 *
 * @param {Object}   props
 * @param {number}   props.value                 Selected page ID (0 = none).
 * @param {Function} props.onChange              Receives the chosen page ID.
 * @param {string}   [props.label]               Field label.
 * @param {string}   [props.help]                Field help text.
 * @param {boolean}  [props.hideLabelFromVision] Visually hide the label.
 * @return {JSX.Element}
 */
const PageSelect = ({ value, onChange, label, help, hideLabelFromVision }) => {
	const pageId = parseInt(value, 10) || 0
	const [search, setSearch] = useState('')
	const setSearchDebounced = useDebounce(setSearch, 300)

	const { pages, current, isSearching } = useSelect(
		(select) => {
			const core = select(coreStore)
			const query = {
				per_page: PER_PAGE,
				_fields: QUERY_FIELDS,
				orderby: 'title',
				order: 'asc',
			}
			if (search) {
				query.search = search
			}

			return {
				pages: core.getEntityRecords('postType', 'page', query),
				isSearching: !core.hasFinishedResolution('getEntityRecords', [
					'postType',
					'page',
					query,
				]),
				// Resolve the saved ID to its title so the field shows the
				// page name on load, not a stale empty box.
				current: pageId
					? core.getEntityRecord('postType', 'page', pageId, {
							_fields: QUERY_FIELDS,
					  })
					: null,
			}
		},
		[search, pageId],
	)

	const options = useMemo(() => {
		const list = (pages || []).map((page) => ({
			value: String(page.id),
			label: pageLabel(page),
		}))

		// Keep the selected page visible even when it falls outside the
		// current search results, so the control never shows a bare ID
		// for an already-saved value.
		if (current && !list.some((opt) => opt.value === String(current.id))) {
			list.unshift({
				value: String(current.id),
				label: pageLabel(current),
			})
		}

		return list
	}, [pages, current])

	// ComboboxControl wraps itself in an unstyled focus-outside <div> that
	// becomes the flex child in a PanelRow and collapses to content width,
	// so `.components-combobox-control`'s own `width: 100%` has nothing to
	// fill. Wrapping it here gives us a styleable flex child — see the
	// `d404-page-select` rule in the shared form styles.
	return (
		<div className="d404-page-select">
			<ComboboxControl
				__next40pxDefaultSize
				__nextHasNoMarginBottom
				label={label}
				help={help}
				hideLabelFromVision={hideLabelFromVision}
				placeholder={__('Search for a page…', '404-to-301')}
				// Swaps the suggestions list for a spinner while a query is
				// in flight, so a slow fetch doesn't look like "no results".
				isLoading={isSearching}
				value={pageId ? String(pageId) : null}
				options={options}
				onFilterValueChange={(next) => setSearchDebounced(next)}
				onChange={(next) => onChange(parseInt(next, 10) || 0)}
			/>
		</div>
	)
}

export default PageSelect
