import { __ } from '@wordpress/i18n'
import { useDispatch, useSelect } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { store as coreStore, useEntityProp } from '@wordpress/core-data'

const useSettings = () => {
	// Get the settings data.
	const [ settings, setSettings ] = useEntityProp(
		'root',
		'site',
		'404_to_301_settings'
	)

	const { saveEditedEntityRecord } = useDispatch( coreStore )

	// Notice store.
	const { createSuccessNotice, createErrorNotice } =
		useDispatch( noticesStore )

	const { isSaving, lastError, hasLoaded } = useSelect(
		( select ) => ( {
			// Flag to check if settings are being updated.
			isSaving: select( coreStore ).isSavingEntityRecord(
				'root',
				'site'
			),
			// Get the last error details.
			lastError: select( coreStore ).getLastEntitySaveError(
				'root',
				'site'
			),
			// Check if settings are loaded.
			hasLoaded: select( coreStore ).hasFinishedResolution(
				'getEntityRecord',
				[ 'root', 'site' ]
			),
		} ),
		[]
	)

	/**
	 * Save the latest setting via API.
	 *
	 * @return {Promise<void>}
	 */
	const saveSettings = async () => {
		// Save using api.
		const savedRecord = await saveEditedEntityRecord( 'root', 'site' )

		// Create success notice.
		if ( ! savedRecord ) {
			createErrorNotice(
				__( 'Error while updating settings.', '404-to-301' )
			)
		} else {
			// Create error notice.
			createSuccessNotice(
				__( 'Successfully updated the settings.', '404-to-301' )
			)
		}
	}

	/**
	 * Handle a setting value change.
	 *
	 * @param {string} key   Setting key.
	 * @param {*}      value Setting value.
	 */
	const handleChange = ( key, value ) => {
		setSettings( {
			...settings,
			[ key ]: value,
		} )
	}

	/**
	 * Get a single setting value.
	 *
	 * @param {string} key          Setting key.
	 * @param {*}      defaultValue Setting default value.
	 */
	const getSetting = ( key, defaultValue = null ) => {
		return settings[ key ] || defaultValue
	}

	// Flag to check if form has changed data.
	const isDirty = useSelect(
		( select ) =>
			select( coreStore ).hasEditsForEntityRecord( 'root', 'site' ),
		[]
	)

	return {
		isDirty,
		isSaving,
		hasLoaded,
		settings,
		lastError,
		getSetting,
		setSettings,
		handleChange,
		saveSettings,
	}
}

export default useSettings
