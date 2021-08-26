<?php
/**
 * ALogs upgrade notice template.
 *
 * @var string $plugin       Plugin name.
 * @var string $upgrade_link Upgrade link.
 * @var string $skip_link    Skip link.
 * @var bool   $upgrading    Is upgrade in progress.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage UpgradeNotice
 */

?>

<div id="dd4t3-notice-upgrade-notice" class="notice duckdev-notice notice-info <?php echo $upgrading ? 'duckdev-show-icon' : ''; ?>">
	<?php if ( $upgrading ) : ?>
		<p>
			<?php
			printf(
			// translators: %s Plugin name.
				__( '<strong>%s</strong> is upgrading old error logs. This notice will disappear when the upgrade process is complete.', '404-to-301' ), // phpcs:ignore
				esc_html( $plugin )
			);
			?>
		</p>
	<?php else : ?>
		<p>
			<?php
			printf(
			// translators: %s Plugin name.
				__( '<strong>%s</strong> needs to upgrade old error logs to new tables. Please note, in order to keep your database clean, we will upgrade only the logs with custom redirects or options set up. Other logs will be deleted.', '404-to-301' ), // phpcs:ignore
				esc_html( $plugin )
			);
			?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $upgrade_link ); ?>"><?php esc_html_e( 'Start Upgrade', '404-to-301' ); ?></a>
			<a class="button" href="<?php echo esc_url( $skip_link ); ?>"><?php esc_html_e( 'Skip Upgrade', '404-to-301' ); ?></a>
		</p>
	<?php endif; ?>
</div>
