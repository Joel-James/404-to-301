import React from 'react'

const {__} = wp.i18n
const {
	Card,
	Button,
	CardHeader,
	CardBody,
	Notice,
	CardFooter,
	__experimentalGrid,
	__experimentalText,
	__experimentalHeading,
} = wp.components

export default (props) => {
	return (
		<>
			{props.addons.length <= 0 ?
				<Notice
					status="info"
					isDismissible={false}
				>
					<p>
						{__('No addons found.', '404-to-301')}
					</p>
				</Notice>
				:
				<__experimentalGrid columns={2}>
					{props.addons.map((addon) =>
						<Card>
							<CardHeader>
								<__experimentalHeading level={5}>{addon.title}</__experimentalHeading>
							</CardHeader>
							<CardBody>
								<__experimentalText>{addon.description}</__experimentalText>
							</CardBody>
							<CardFooter>
								<Button
									variant="secondary"
									icon="external"
									iconPosition="right"
									href={addon.link}
								>
									View Details
								</Button>
							</CardFooter>
						</Card>
					)}
				</__experimentalGrid>
			}
		</>
	)
}