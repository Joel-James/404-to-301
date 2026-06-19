/**
 * Settings hook.
 *
 * Reads and writes the plugin settings through the WordPress
 * `/wp/v2/settings` endpoint via the core-data store. The option is
 * registered server side with `show_in_rest`, so it behaves like any
 * other site setting.
 */
import { __ } from '@wordpress/i18n'
import { useDispatch, useSelect } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { store as coreStore, useEntityProp } from '@wordpress/core-data'

const OPTION_KEY = '404_to_301_settings'

const useSettings = () => {
	const [option, setOption] = useEntityProp('root', 'site', OPTION_KEY)

	const { saveEditedEntityRecord } = useDispatch(coreStore)
	const { createSuccessNotice, createErrorNotice, removeAllNotices } =
		useDispatch(noticesStore)

	const { isSaving, hasLoaded, isDirty } = useSelect((select) => {
		const core = select(coreStore)

		return {
			isSaving: core.isSavingEntityRecord('root', 'site'),
			isDirty: core.hasEditsForEntityRecord('root', 'site'),
			hasLoaded: core.hasFinishedResolution('getEntityRecord', [
				'root',
				'site',
			]),
		}
	}, [])

	const settings = option || {}

	/**
	 * Read a single setting value with a fallback.
	 *
	 * @param {string} key          Setting key.
	 * @param {*}      defaultValue Fallback value.
	 * @return {*} Setting value.
	 */
	const getSetting = (key, defaultValue = '') =>
		settings[key] === undefined ? defaultValue : settings[key]

	/**
	 * Update a single setting value (locally, until saved).
	 *
	 * @param {string} key   Setting key.
	 * @param {*}      value New value.
	 */
	const setSetting = (key, value) => {
		setOption({ ...settings, [key]: value })
	}

	/**
	 * Persist the edited settings via the REST API.
	 */
	const saveSettings = async () => {
		removeAllNotices()

		const saved = await saveEditedEntityRecord('root', 'site')

		if (saved) {
			createSuccessNotice(__('Settings saved.', '404-to-301'), {
				type: 'snackbar',
			})
		} else {
			createErrorNotice(__('Could not save settings.', '404-to-301'), {
				type: 'snackbar',
			})
		}
	}

	return {
		settings,
		hasLoaded,
		isSaving,
		isDirty,
		getSetting,
		setSetting,
		saveSettings,
	}
}

export default useSettings
