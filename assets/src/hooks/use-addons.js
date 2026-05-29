import { useSelect, useDispatch } from '@wordpress/data'
import { STORE_KEY } from '../store/addons'

/**
 * Thin selector / dispatch wrapper around the `d404/addons` store.
 *
 * Component-level API is intentionally the same as the previous
 * `useState`-based hook so consumers (Catalog tab, LicenseModal,
 * etc.) didn't need to change shape:
 *
 *     const { items, isLoading, refresh, activateLicense, ... } = useAddons()
 *
 * The store now owns the catalog state, so multiple consumers
 * share a single fetch. Switching from Addons → Support → Addons
 * no longer triggers a refetch — `getItems()`'s resolver only
 * fires the first time it's read.
 */
const useAddons = () => {
	const { items, isLoading, isRefreshing } = useSelect((select) => {
		const store = select(STORE_KEY)
		return {
			items: store.getItems(),
			isLoading: store.isLoading(),
			isRefreshing: store.isRefreshing(),
		}
	}, [])

	const { refresh, activateLicense, deactivateLicense } =
		useDispatch(STORE_KEY)

	return {
		items,
		isLoading,
		isRefreshing,
		refresh,
		activateLicense,
		deactivateLicense,
	}
}

export default useAddons
