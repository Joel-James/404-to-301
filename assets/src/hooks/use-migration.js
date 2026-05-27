import { __ } from '@wordpress/i18n'
import { useCallback, useEffect, useRef, useState } from '@wordpress/element'
import { useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'
import apiFetch from './use-rest'

const useMigration = () => {
	const [status, setStatus] = useState(null)
	const [isStarting, setIsStarting] = useState(false)

	// Ref so the tick loop can read the latest "should I keep going" flag
	// without re-creating the effect on every status change.
	const loopActive = useRef(false)

	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)

	const fetchStatus = useCallback(async () => {
		try {
			const next = await apiFetch('migration')
			setStatus(next)
			return next
		} catch (err) {
			return null
		}
	}, [])

	// One-shot initial status load.
	useEffect(() => {
		fetchStatus()
	}, [fetchStatus])

	/**
	 * Drive the migration forward — keeps POSTing /migration/tick until
	 * the server says there are no rows left, the user aborts, or the
	 * tab closes. Each tick processes a chunk server-side and returns
	 * the freshest status.
	 */
	const runLoop = useCallback(async () => {
		if (loopActive.current) {
			return
		}
		loopActive.current = true

		try {
			while (loopActive.current) {
				let next
				try {
					next = await apiFetch('migration/tick', { method: 'POST' })
				} catch (err) {
					createErrorNotice(
						err.message ||
							__(
								'Migration tick failed — will retry on next page load.',
								'404-to-301',
							),
						{ type: 'snackbar' },
					)
					break
				}

				setStatus(next)

				// Server signals done either by clearing the legacy
				// table or by setting logs_migrated=true.
				if (!next || !next.running || next.remaining <= 0) {
					createSuccessNotice(
						__('Migration complete.', '404-to-301'),
						{ type: 'snackbar' },
					)
					break
				}

				// Tiny breather between ticks so the UI can repaint
				// the progress counter.
				await new Promise((r) => setTimeout(r, 250))
			}
		} finally {
			loopActive.current = false
		}
	}, [createErrorNotice, createSuccessNotice])

	const start = async () => {
		setIsStarting(true)

		try {
			// The server's `start` processes one chunk inline so the
			// counter visibly drops immediately, then we drive the
			// rest from the loop.
			const next = await apiFetch('migration', { method: 'POST' })
			setStatus(next)

			if (next && next.running && next.remaining > 0) {
				runLoop()
			} else {
				createSuccessNotice(
					__('Migration complete.', '404-to-301'),
					{ type: 'snackbar' },
				)
			}
		} catch (err) {
			createErrorNotice(
				err.message ||
					__('Could not start the migration.', '404-to-301'),
				{ type: 'snackbar' },
			)
		} finally {
			setIsStarting(false)
		}
	}

	const abort = async () => {
		// Stop the loop before sending the abort so the next iteration
		// doesn't re-queue a chunk.
		loopActive.current = false

		try {
			const next = await apiFetch('migration', { method: 'DELETE' })
			setStatus(next)
			createSuccessNotice(__('Migration aborted.', '404-to-301'), {
				type: 'snackbar',
			})
		} catch (err) {
			createErrorNotice(
				err.message ||
					__('Could not abort the migration.', '404-to-301'),
				{ type: 'snackbar' },
			)
		}
	}

	// If the page loads and the migration is already running (eg. the
	// admin reopens the tab while a background tick is mid-flight),
	// resume the loop client-side too so the counter keeps ticking
	// without waiting for cron.
	useEffect(() => {
		if (status && status.running && status.remaining > 0 && !loopActive.current) {
			runLoop()
		}
	}, [status, runLoop])

	// Stop the loop when the component unmounts.
	useEffect(
		() => () => {
			loopActive.current = false
		},
		[],
	)

	return { status, isStarting, start, abort, refresh: fetchStatus }
}

export default useMigration
