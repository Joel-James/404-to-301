import { Popover, SlotFillProvider } from '@wordpress/components'
import ErrorBoundary from './error-boundary'

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
 * The page tree is wrapped in an {@see ErrorBoundary} so a render-time
 * throw degrades to an inline error notice instead of unmounting the
 * whole root to a blank page.
 *
 * @param {Object} props
 * @param {Object} props.children The page's React tree.
 */
const AppShell = ({ children }) => (
	<SlotFillProvider>
		<ErrorBoundary>{children}</ErrorBoundary>
		<Popover.Slot />
	</SlotFillProvider>
)

export default AppShell
