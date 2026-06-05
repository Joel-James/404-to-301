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
import { buildFilename, downloadEnvelope } from '../lib/download'

// REST routes for the settings envelope. Centralised here so a route
// rename in PHP only needs one JS-side update.
const EXPORT_PATH = '/404-to-301/v1/settings/export'
const IMPORT_PATH = '/404-to-301/v1/settings/import'

/**
 * Settings Import / Export panel.
 *
 * Lives in the Tools tab. Wraps:
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

	/**
	 * Export click handler. Fetches the envelope from REST and hands
	 * it to the browser as a JSON download. Errors surface via the
	 * notices store so they appear in the standard admin notice slot.
	 */
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

	/**
	 * File picker `change` handler. Reads the selected file as text,
	 * parses it as JSON, POSTs it to the import endpoint and reloads
	 * the page so every mounted React app (settings, logs config,
	 * etc.) re-reads the freshly-imported option from the server.
	 *
	 * Accepts either a full envelope (the export format) or a bare
	 * `settings` object — the server normalises both shapes.
	 *
	 * @param {Event} event Native `change` event from the hidden input.
	 */
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
