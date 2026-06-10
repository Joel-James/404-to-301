import { __ } from '@wordpress/i18n'
import { PanelBody, PanelRow } from '@wordpress/components'
import { applyFilters } from '@wordpress/hooks'
import useSettings from '../../../hooks/use-settings'
import PathsRepeater from '../components/paths-repeater'
import ImportExport from '../components/import-export'

const Tools = () => {
	const { getSetting, setSetting } = useSettings()

	/*
	 * Addon extension point. Mirrors `d404.settings.general.fields` — add-ons
	 * can hook in to inject controls at the end of the Tools tab.
	 */
	const extra = applyFilters('d404.settings.tools.fields', null, {
		getSetting,
		setSetting,
	})

	/*
	 * Cross-sell slot. No default promo today, but the filter exists so
	 * addons can inject one without a future parent-side code change —
	 * matches the symmetry every other tab follows.
	 */
	const crossSell = applyFilters('d404.settings.tools.cross_sell', null)

	return (
		<>
			<PanelBody title={__('Exclude Paths', '404-to-301')}>
				<PanelRow>
					<div className="d404-repeater-field">
						<div className="d404-repeater-field__label">
							{__('Paths to ignore', '404-to-301')}
						</div>
						<PathsRepeater
							value={getSetting('exclude_paths', [])}
							onChange={(v) => setSetting('exclude_paths', v)}
							placeholder={__(
								'e.g. /wp-json/ or /feed/',
								'404-to-301',
							)}
						/>
						<p className="components-base-control__help">
							{__(
								'Any 404 whose URL contains one of these substrings is skipped — no log, no redirect, no email.',
								'404-to-301',
							)}
						</p>
					</div>
				</PanelRow>
			</PanelBody>

			<ImportExport />

			{extra}

			{crossSell}
		</>
	)
}

export default Tools
