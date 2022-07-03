/* global wp */
import React, { useState } from 'react'
import { useInstanceId } from '@wordpress/compose'

const { __ } = wp.i18n

/**
 * Search input component for the listing table header.
 *
 * @since 4.0.0
 *
 * @param {*} param0
 * @returns {*}
 */
const SearchBox = ({ onSearch }) => {
	const id = useInstanceId(SearchBox)
	const [search, setSearch] = useState('')

	return (
		<p className="search-box">
			<label
				className="screen-reader-text"
				htmlFor={`search-input-${id}`}
			>
				{__('Search:', '404-to-301')}
			</label>
			<input
				type="search"
				id={`search-input-${id}`}
				name={`search-input-${id}`}
				value={search}
				onChange={(ev) => setSearch(ev.target.value)}
			/>
			<button
				type="button"
				className="button"
				onClick={() => onSearch(search)}
			>
				{__('Search', '404-to-301')}
			</button>
		</p>
	)
}

export default SearchBox
