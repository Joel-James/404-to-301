import { __ } from '@wordpress/i18n'
import Badge from './badge'

/**
 * License-state badge — returns the matching `<Badge />` or `null`
 * when there's nothing meaningful to show. We only paint it for
 * addons that are locally registered, otherwise users get a
 * misleading "Unlicensed" for addons they haven't even installed.
 *
 * @param {Object}  props
 * @param {boolean} props.isActive          Whether the addon is registered locally.
 * @param {boolean} props.isLicenseActive   Whether the stored license is activated.
 * @return {JSX.Element|null}
 */
const LicenseBadge = ({ isActive, isLicenseActive }) => {
	if (!isActive) {
		return null
	}

	return isLicenseActive ? (
		<Badge kind="success">{__('Licensed', '404-to-301')}</Badge>
	) : (
		<Badge kind="warning">{__('Unlicensed', '404-to-301')}</Badge>
	)
}

export default LicenseBadge
