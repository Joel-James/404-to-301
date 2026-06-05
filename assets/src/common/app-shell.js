import { Popover, SlotFillProvider } from '@wordpress/components'

/**
 * Root wrapper every admin page mounts inside.
 *
 * The `@wordpress/components` library uses the SlotFill mechanism to
 * render Popovers, Dropdowns and Menus into a dedicated slot at the
 * top of the React tree. Without a `SlotFillProvider` + a rendered
 * `<Popover.Slot />`, popovers fall back to rendering at the body
 * root with broken positioning and a transparent background — which
 * is why filter / per-column menus on DataViews look blurred or
 * ghosted.
 *
 * @param {Object} props
 * @param {Object} props.children The page's React tree.
 */
const AppShell = ({ children }) => (
	<SlotFillProvider>
		{children}
		<Popover.Slot />
	</SlotFillProvider>
)

export default AppShell
