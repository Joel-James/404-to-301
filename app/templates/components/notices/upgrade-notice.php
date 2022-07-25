<?php
/**
 * ALogs upgrade notice template.
 *
 * @link       https://duckdev.com/products/404-to-301/
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @var string $plugin              Plugin name.
 * @var bool   $upgrading           Is upgrade in progress.
 * @var bool   $scheduler_available Is action scheduler available.
 *
 * @copyright  Copyright (c) 2022, Joel James
 * @package    View
 * @subpackage UpgradeNotice
 */

$skip = wp_nonce_url(
	add_query_arg( 'dd4t3_db_upgrade', 'skip' ),
	'dd4t3_db_upgrade',
	'dd4t3_nonce'
);

$upgrade_all = wp_nonce_url(
	add_query_arg( 'dd4t3_db_upgrade', 'upgrade_all' ),
	'dd4t3_db_upgrade',
	'dd4t3_nonce'
);

$upgrade_redirects = wp_nonce_url(
	add_query_arg( 'dd4t3_db_upgrade', 'upgrade_redirects' ),
	'dd4t3_db_upgrade',
	'dd4t3_nonce'
);

$scheduler_install = wp_nonce_url(
	add_query_arg(
		array(
			'action' => 'install-plugin',
			'plugin' => 'action-scheduler',
		),
		admin_url( 'update.php' )
	),
	'install-plugin_action-scheduler'
);

?>

<div id="dd4t3-notice-upgrade-notice" class="notice dd4t3-redirects-notice notice-info <?php echo $upgrading ? 'dd4t3-redirects-show-icon' : ''; ?>">
	<?php if ( $upgrading ) : ?>
		<p>
			<?php
			printf(
			// translators: %s Plugin name.
				esc_attr__( '%s is upgrading old error logs. It may take a while depending on the number of logs. This notice will disappear when the upgrade process is complete.', '404-to-301' ),
				'<strong>' . esc_html( $plugin ) . '</strong>'
			);
			?>
		</p>
	<?php elseif ( $scheduler_available ) : ?>
		<p>
			<?php
			printf(
			// translators: %s Plugin name.
				esc_attr__( '%s needs to upgrade old error logs to new tables. If you have a lot of old logs, we recommend to upgrade only custom redirected logs to minimise the database size.', '404-to-301' ),
				'<strong>' . esc_html( $plugin ) . '</strong>'
			);
			?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $upgrade_redirects ); ?>"><?php esc_html_e( 'Upgrade custom redirects', '404-to-301' ); ?></a>
			<a
				class="button"
				onclick="return confirm('<?php esc_html_e( 'Are you sure? This may take very long if you have a lot of logs to upgrade. But do not worry. It will run in background.', '404-to-301' ); ?>')"
				href="<?php echo esc_url( $upgrade_all ); ?>"
			>
				<?php esc_html_e( 'Upgrade everything', '404-to-301' ); ?>
			</a>
		</p>
		<p>
			<a
				href="<?php echo esc_url( $skip ); ?>"
				onclick="return confirm('<?php esc_html_e( 'Are you sure? The old logs will be removed permanently.', '404-to-301' ); ?>')"
				style="color: red;"
			>
				<?php esc_html_e( 'Do not upgrade', '404-to-301' ); ?>
			</a>
		</p>
	<?php else : ?>
		<p>
			<?php
			printf(
			// translators: %s Plugin name.
				esc_attr__( '%s needs to upgrade old error logs to new tables. To start upgrade in the background you will have to install Action Scheduler plugin.', '404-to-301' ),
				'<strong>' . esc_html( $plugin ) . '</strong>'
			);
			?>
		</p>
		<p>
			<a class="button button-primary" href="<?php echo esc_url( $scheduler_install ); ?>"><?php esc_html_e( 'Install Action Scheduler', '404-to-301' ); ?></a>
			<a class="button" href="<?php echo esc_url( $skip ); ?>"><?php esc_html_e( 'Do Not Upgrade', '404-to-301' ); ?></a>
		</p>
	<?php endif; ?>
</div>
