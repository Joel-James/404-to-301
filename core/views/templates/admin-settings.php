<?php
/**
 * Admin settings page base template.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 *
 * @var array  $tabs    Tabs list.
 * @var string $current Current tab.
 *
 * @subpackage Pages
 */

?>

<div class="container max-w-screen-xl px-4 sm:px-2 lg:px-4 py-6">
	<div class="lg:grid lg:grid-cols-12 lg:gap-x-5">
		<aside class="py-6 px-2 sm:px-6 lg:py-0 lg:px-0 lg:col-span-3">
			<nav class="space-y-1">
				<?php foreach ( $tabs as $tab => $details ) : // phpcs:ignore ?>
					<a
							href="<?php echo add_query_arg( 'tab', $tab ); ?>"
							class="<?php echo esc_attr( $this->get_current_class( $tab ) ); ?>"
							aria-current="page"
					>
						<span class="flex-shrink-0 -ml-1 mr-3 w-6 dashicons dashicons-<?php echo esc_attr( $details['icon'] ); ?>"></span>
						<span class="truncate">
						<?php echo esc_attr( $details['label'] ); ?>
					</span>
					</a>
				<?php endforeach; ?>
			</nav>
		</aside>
		<div class="space-y-6 sm:px-6 lg:px-0 lg:col-span-9">
			<div id="dd-404-to-301-settings-<?php echo esc_attr( $current ); ?>"></div>
		</div>
	</div>
</div>
