import { __ } from '@wordpress/i18n'
import {
	Button,
	Card,
	CardBody,
	CardDivider,
	CardFooter,
	CardHeader,
	Flex,
	FlexBlock,
	FlexItem,
} from '@wordpress/components'

/**
 * Compact status pill used in the card header.
 */
const Badge = ({ children, kind = 'default' }) => (
	<span className={`d404-addon-badge d404-addon-badge--${kind}`}>
		{children}
	</span>
)

const licenseBadge = (status) => {
	switch (status) {
		case 'active':
			return <Badge kind="success">{__('Licensed', '404-to-301')}</Badge>
		case 'inactive':
			return <Badge kind="warning">{__('Inactive', '404-to-301')}</Badge>
		default:
			return null
	}
}

/**
 * Single addon card.
 *
 * Uses the @wordpress/components Card primitives in their stock
 * elevated-and-rounded configuration so we inherit the native
 * shadow / border / spacing rather than re-inventing them.
 *
 * @param {Object}   props
 * @param {Object}   props.addon            Addon row (decorated with license status).
 * @param {Function} props.onManageLicense  Called with the addon when the licence button is clicked.
 */
const AddonCard = ({ addon, onManageLicense }) => {
	const installCta = addon.installed
		? addon.active
			? __('Active', '404-to-301')
			: __('Activate plugin', '404-to-301')
		: __('Get this add-on', '404-to-301')

	return (
		<Card className="d404-addon-card" isRounded size="small">
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
						{licenseBadge(addon.license_status)}
					</Flex>
				</FlexItem>
			</CardHeader>

			<CardBody>
				<p style={{ margin: 0 }}>{addon.description}</p>

				{Array.isArray(addon.tags) && addon.tags.length > 0 && (
					<>
						<CardDivider />
						<Flex gap={1} wrap className="d404-addon-tags">
							{addon.tags.map((tag) => (
								<Badge key={tag} kind="tag">
									{tag}
								</Badge>
							))}
						</Flex>
					</>
				)}
			</CardBody>

			<CardFooter>
				<FlexItem>
					<Button
						variant={addon.active ? 'tertiary' : 'primary'}
						href={addon.cta_url || undefined}
						target={addon.cta_url ? '_blank' : undefined}
						rel={addon.cta_url ? 'noopener noreferrer' : undefined}
						disabled={addon.active}
					>
						{installCta}
					</Button>
				</FlexItem>

				{addon.has_license && (
					<FlexItem>
						<Button
							variant="secondary"
							onClick={() => onManageLicense(addon)}
						>
							{addon.license_status === 'active'
								? __('Manage license', '404-to-301')
								: __('Activate license', '404-to-301')}
						</Button>
					</FlexItem>
				)}
			</CardFooter>
		</Card>
	)
}

export default AddonCard
