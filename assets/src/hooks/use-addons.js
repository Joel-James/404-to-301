import { __ } from '@wordpress/i18n'
import { useCallback, useEffect, useState } from '@wordpress/element'
import { useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import apiFetch from './use-rest'

const useAddons = () => {
	const [items, setItems] = useState([])
	const [isLoading, setIsLoading] = useState(true)
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)

	const load = useCallback(
		async (force = false) => {
			setIsLoading(true)
			try {
				const data = await apiFetch(
					`addons${force ? '?force=1' : ''}`,
				)
				setItems(data.items || [])
			} catch (err) {
				createErrorNotice(
					__('Could not load the addons catalog.', '404-to-301'),
					{ type: 'snackbar' },
				)
			} finally {
				setIsLoading(false)
			}
		},
		[createErrorNotice],
	)

	useEffect(() => {
		load()
	}, [load])

	/**
	 * Merge an updated addon row back into the in-memory list so the
	 * UI reflects the new licence state without a full reload.
	 */
	const replaceAddon = useCallback((next) => {
		if (!next || !next.slug) {
			return
		}
		setItems((current) =>
			current.map((addon) =>
				addon.slug === next.slug ? { ...addon, ...next } : addon,
			),
		)
	}, [])

	const activateLicense = async (slug, key) => {
		try {
			const data = await apiFetch(
				`addons/${encodeURIComponent(slug)}/license`,
				{
					method: 'POST',
					body: JSON.stringify({ key }),
				},
			)

			if (data.success) {
				createSuccessNotice(__('License activated.', '404-to-301'), {
					type: 'snackbar',
				})
				replaceAddon(data.addon)
				return data.addon || null
			}

			createErrorNotice(__('License activation failed.', '404-to-301'), {
				type: 'snackbar',
			})
			return null
		} catch (err) {
			createErrorNotice(
				err.message || __('License activation failed.', '404-to-301'),
				{ type: 'snackbar' },
			)
			return null
		}
	}

	const deactivateLicense = async (slug) => {
		try {
			const data = await apiFetch(
				`addons/${encodeURIComponent(slug)}/license`,
				{ method: 'DELETE' },
			)

			if (data.success) {
				createSuccessNotice(
					__('License deactivated.', '404-to-301'),
					{ type: 'snackbar' },
				)
				replaceAddon(data.addon)
				return data.addon || null
			}

			createErrorNotice(
				__('License deactivation failed.', '404-to-301'),
				{ type: 'snackbar' },
			)
			return null
		} catch (err) {
			createErrorNotice(
				err.message ||
					__('License deactivation failed.', '404-to-301'),
				{ type: 'snackbar' },
			)
			return null
		}
	}

	return {
		items,
		isLoading,
		reload: load,
		activateLicense,
		deactivateLicense,
	}
}

export default useAddons
