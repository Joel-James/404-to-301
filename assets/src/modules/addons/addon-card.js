import { __ } from '@wordpress/i18n'
import {
	Button,
	Card,
	CardBody,
	CardFooter,
	CardHeader,
	CardMedia,
	Flex,
	FlexBlock,
	FlexItem,
} from '@wordpress/components'
import BannerImage from './components/banner-image'
import Badge from './components/badge'
import LicenseBadge from './components/license-badge'

/**
 * Single addon card.
 *
 * Card chrome comes from the `@wordpress/components` Card primitives.
 * The card has a fixed-height footer; the description body is the
 * flex-grower so short descriptions leave whitespace below the text
 * rather than ballooning the footer.
 *
 * License management is handled in a modal triggered by the "Manage
 * license" button. The modal is rendered by the parent page so
 * keyboard focus and ARIA live announcements stay clean.
 *
 * @param {Object}   props
 * @param {Object}   props.addon              Decorated addon row coming from REST.
 * @param {Function} props.onManageLicense    Called with the addon when the licence button is clicked.
 */
const AddonCard = ({ addon, onManageLicense }) => {
	/**
	 * Footer CTA shown when the addon is NOT registered locally — a
	 * Buy/Get link that opens the external checkout / WP.org page.
	 *
	 * For active addons we render the manage-license button + a
	 * plain "Active" label instead (see the JSX below); the two
	 * states are mutually exclusive so we only need one of them at
	 * a time.
	 */
	const purchaseCta = (
		<Button
			variant={addon.is_premium ? 'primary' : 'secondary'}
			href={addon.link || addon.homepage || undefined}
			target="_blank"
			rel="noopener noreferrer"
		>
			{addon.is_premium
				? __('Buy Now', '404-to-301')
				: __('Get it', '404-to-301')}
		</Button>
	)

	return (
		<Card className="d404-addon-card" isRounded size="small">
			{/*
			  * Optional marketing banner. Freemius returns this as
			  * `info.card_banner_url` and our REST layer surfaces it
			  * as `addon.banner`. Render only when populated so cards
			  * without a banner image still look intentional.
			  *
			  * When the addon is locally installed + active we also
			  * paint an "Active" pill in the top-right corner of the
			  * banner. The banner area itself sits on a black canvas
			  * so the pill uses a high-contrast green-on-white look.
			  */}
			{addon.banner && (
				<CardMedia className="d404-addon-banner">
					<BannerImage src={addon.banner} alt={addon.title} />
					{addon.is_active && (
						<span
							className="d404-addon-status"
							aria-label={__('Active', '404-to-301')}
						>
							{__('Active', '404-to-301')}
						</span>
					)}
				</CardMedia>
			)}

			<CardHeader>
				<FlexBlock>
					<strong>{addon.title}</strong>
				</FlexBlock>
				<FlexItem>
					<Flex gap={1} align="center" justify="flex-end">
						{addon.is_premium ? (
							<Badge kind="premium">
								{__('Premium', '404-to-301')}
							</Badge>
						) : (
							<Badge kind="free">
								{__('Free', '404-to-301')}
							</Badge>
						)}
						<LicenseBadge
							isActive={addon.is_active}
							isLicenseActive={addon.is_license_active}
						/>
					</Flex>
				</FlexItem>
			</CardHeader>

			{/*
			  * Description body. Marked `d404-addon-description` so the
			  * SCSS can pin `flex: 1` on it specifically — that way
			  * the description area soaks up any vertical slack
			  * needed to equalise card heights inside the grid, while
			  * the footer stays at its natural content height.
			  */}
			<CardBody className="d404-addon-description">
				<p style={{ margin: 0 }}>{addon.description}</p>
			</CardBody>

			<CardFooter>
				{/*
				  * Left side — the primary control. For active addons
				  * it's the Manage License button; otherwise it's the
				  * Buy / Get CTA. The "Active" indicator now lives
				  * inside the banner (top-right corner) rather than
				  * here in the footer.
				  */}
				<FlexItem>
					{addon.is_active ? (
						<Button
							variant="secondary"
							onClick={() => onManageLicense(addon)}
						>
							{addon.is_license_active
								? __('Manage License', '404-to-301')
								: __('Activate License', '404-to-301')}
						</Button>
					) : (
						purchaseCta
					)}
				</FlexItem>

				{/* Right side — "More details" link, pinned right. */}
				{addon.homepage && (
					<FlexItem>
						<Button
							variant="link"
							href={addon.homepage}
							target="_blank"
							rel="noopener noreferrer"
						>
							{__('More details', '404-to-301')}
						</Button>
					</FlexItem>
				)}
			</CardFooter>
		</Card>
	)
}

export default AddonCard
