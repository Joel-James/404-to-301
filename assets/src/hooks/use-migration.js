import { __ } from '@wordpress/i18n'
import { useCallback, useEffect, useRef } from '@wordpress/element'
import { useDispatch, useSelect } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import { STORE_KEY } from '../store/migration'

/**
 * Migration banner driver.
 *
 * State (status, isStarting) lives in the `d404/migration` store —
 * the resolver fires the initial fetch on first read of
 * `getStatus()`. The polling side-effect (POST `/migration/tick`
 * until done) stays in this hook because setTimeout chains belong
 * at the React level, not in the store.
 *
 * Public shape is unchanged from the previous useState-only
 * implementation so the banner component doesn't need to know
 * we're talking to a store.
 */
const useMigration = () => {
	const { status, isStarting } = useSelect((select) => {
		const store = select(STORE_KEY)
		return {
			status: store.getStatus(),
			isStarting: store.isStarting(),
		}
	}, [])

	const { start: dispatchStart, abort: dispatchAbort, tick } =
		useDispatch(STORE_KEY)
	const { createSuccessNotice } = useDispatch(noticesStore)

	// Whether the tick loop is currently running. Held in a ref so
	// the loop body can `loopActive.current` to check liveness
	// across awaits without re-creating the function.
	const loopActive = useRef(false)

	/**
	 * Drive the migration forward.
	 *
	 * Pings the tick endpoint until the server reports completion,
	 * the user aborts, or the page unmounts. Tiny breather between
	 * iterations so the UI can repaint the progress counter.
	 */
	const runLoop = useCallback(async () => {
		if (loopActive.current) {
			return
		}
		loopActive.current = true

		try {
			while (loopActive.current) {
				// eslint-disable-next-line no-await-in-loop -- sequential ticks are the point
				const next = await tick()

				// Server signals done either by clearing the legacy
				// table or by setting `logs_migrated=true`.
				if (!next || !next.running || next.remaining <= 0) {
					createSuccessNotice(
						__('Migration complete.', '404-to-301'),
					)
					break
				}

				// eslint-disable-next-line no-await-in-loop
				await new Promise((r) => setTimeout(r, 250))
			}
		} finally {
			loopActive.current = false
		}
	}, [tick, createSuccessNotice])

	/**
	 * Start handler used by the banner — dispatches the store's
	 * `start` action and, on success, kicks off the loop.
	 */
	const start = useCallback(async () => {
		const next = await dispatchStart()

		if (next && next.running && next.remaining > 0) {
			runLoop()
		}
	}, [dispatchStart, runLoop])

	/**
	 * Abort handler — stops the loop locally before sending the
	 * server-side abort so the next iteration doesn't sneak in
	 * another tick mid-flight.
	 */
	const abort = useCallback(async () => {
		loopActive.current = false
		await dispatchAbort()
	}, [dispatchAbort])

	// Resume the loop if the page is reopened while a migration is
	// already in-flight (e.g. another tab kicked it off, or a
	// scheduled wp-cron tick is mid-batch). The selector will
	// auto-resolve the initial status; once that lands, this effect
	// notices the `running` flag and starts ticking client-side too.
	useEffect(() => {
		if (
			status &&
			status.running &&
			status.remaining > 0 &&
			!loopActive.current
		) {
			runLoop()
		}
	}, [status, runLoop])

	// Stop ticking when the banner unmounts.
	useEffect(
		() => () => {
			loopActive.current = false
		},
		[],
	)

	return { status, isStarting, start, abort }
}

export default useMigration
