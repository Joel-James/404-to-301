const TabContent = ( { currentTab, tabs } ) => {
	const TabComponent = tabs[ currentTab ]

	return <TabComponent />
}

export default TabContent
