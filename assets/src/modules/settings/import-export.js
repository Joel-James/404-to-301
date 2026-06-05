import { __ } from '@wordpress/i18n'
import { useRef, useState } from '@wordpress/element'
import {
	Button,
	Notice,
	PanelBody,
	PanelRow,
} from '@wordpress/components'
import apiFetch from '@wordpress/api-fetch'
import { useDispatch } from '@wordpress/data'
import { store as noticesStore } from '@wordpress/notices'

const EXPORT_PATH = '/404-to-301/v1/settings/export'
const IMPORT_PATH = '/404-to-301/v1/settings/import'

/**
 * Build the filename used for downloaded exports. The site host gives
 * staging vs prod files distinct names so they don't collide in a
 * Downloads folder, and the date suffix makes versions obvious.
 */
const buildFilename = () => {
	const host = (window.location?.hostname || 'site').replace(/[^a-z0-9.-]/gi, '')
	const date = new Date().toISOString().slice(0, 10)
	return `404-to-301-settings-${host}-${date}.json`
}

/**
 * Trigger a client-side download for a JSON envelope.
 *
 * We can't just point an `<a>` at the REST endpoint — apiFetch handles
 * the `X-WP-Nonce` header for us, and a bare link would be rejected by
 * the permission_callback. So we fetch the body, build a Blob URL, and
 * synthesise a one-shot anchor click.
 */
const downloadEnvelope = (envelope, filename) => {
	const blob = new Blob([JSON.stringify(envelope, null, 2)], {
		type: 'application/json',
	})
	const url = URL.createObjectURL(blob)
	const link = document.createElement('a')
	link.href = url
	link.download = filename
	document.body.appendChild(link)
	link.click()
	document.body.removeChild(link)
	URL.revokeObjectURL(url)
}

/**
 * Settings Import / Export panel.
 *
 * Rendered at the bottom of the General tab. Wraps:
 *   - Export: GET /settings/export, download the response as JSON.
 *   - Import: pick a JSON file, POST its contents to /settings/import,
 *     refresh the settings store so the UI reflects the new state.
 *
 * The store refresh after import is important: the React form is the
 * source of truth for unsaved edits, and we'd otherwise show stale
 * values from before the import.
 */
const ImportExport = () => {
	const fileInputRef = useRef(null)
	const [busy, setBusy] = useState(false)
	const [importedSummary, setImportedSummary] = useState(null)

	const { createSuccessNotice, createErrorNotice } = useDispatch(noticesStore)

	const handleExport = async () => {
		setBusy(true)
		try {
			const envelope = await apiFetch({ path: EXPORT_PATH })
			downloadEnvelope(envelope, buildFilename())
			createSuccessNotice(__('Settings exported.', '404-to-301'))
		} catch (e) {
			createErrorNotice(
				e?.message || __('Failed to export settings.', '404-to-301'),
			)
		} finally {
			setBusy(false)
		}
	}

	const handleFile = async (event) => {
		const file = event.target.files?.[0]
		// Reset the input value so picking the same file again still
		// fires `change` — browsers suppress duplicate selections by
		// default.
		event.target.value = ''
		if (!file) {
			return
		}

		setBusy(true)
		setImportedSummary(null)
		try {
			const text = await file.text()
			let payload
			try {
				payload = JSON.parse(text)
			} catch {
				throw new Error(
					__('That file is not valid JSON.', '404-to-301'),
				)
			}

			// Accept either a full envelope (the export format) or the
			// raw `settings` object — the server handles both shapes.
			const body = { settings: payload }
			const response = await apiFetch({
				path: IMPORT_PATH,
				method: 'POST',
				data: body,
			})

			setImportedSummary({
				count: Number(response?.imported || 0),
				file: file.name,
			})
			createSuccessNotice(__('Settings imported.', '404-to-301'))
			// Hard reload so the React form re-reads the option from
			// the server. Invalidating the core-data entity in place
			// would be subtler, but imports are rare and a reload
			// guarantees every tab in the page (logs config, etc.)
			// sees the new values too.
			window.setTimeout(() => window.location.reload(), 600)
		} catch (e) {
			createErrorNotice(
				e?.message || __('Failed to import settings.', '404-to-301'),
			)
		} finally {
			setBusy(false)
		}
	}

	return (
		<PanelBody title={__('Import / Export', '404-to-301')}>
			<PanelRow>
				<p className="components-base-control__help">
					{__(
						'Move plugin settings between sites — eg. from staging to production. The export contains every user-facing setting; install-specific state (database versions, migration flags) is excluded.',
						'404-to-301',
					)}
				</p>
			</PanelRow>

			<PanelRow>
				<div className="d404-import-export-actions">
					<Button
						variant="secondary"
						isBusy={busy}
						disabled={busy}
						onClick={handleExport}
					>
						{__('Download settings', '404-to-301')}
					</Button>

					<Button
						variant="secondary"
						isBusy={busy}
						disabled={busy}
						onClick={() => fileInputRef.current?.click()}
					>
						{__('Import settings…', '404-to-301')}
					</Button>

					<input
						ref={fileInputRef}
						type="file"
						accept="application/json,.json"
						onChange={handleFile}
						style={{ display: 'none' }}
					/>
				</div>
			</PanelRow>

			{importedSummary && (
				<PanelRow>
					<Notice status="success" isDismissible={false}>
						{__(
							'Applied %1$d setting(s) from %2$s.',
							'404-to-301',
						)
							.replace('%1$d', String(importedSummary.count))
							.replace('%2$s', importedSummary.file)}
					</Notice>
				</PanelRow>
			)}
		</PanelBody>
	)
}

export default ImportExport
