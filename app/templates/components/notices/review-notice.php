<?php
/**
 * Admin review notice template.
 *
 * @var string $user_name User's name.
 *
 * @author     Joel James <me@joelsays.com>
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2020, Joel James
 * @link       https://duckdev.com/products/404-to-301/
 * @package    View
 * @subpackage ReviewNotice
 */

// Make sure we have name.
$name = empty( $user_name ) ? __( 'friend', '404-to-301' ) : ucwords( $user_name );

?>

<div id="dd404-notice-review-notice" class="notice duckdev-notice notice-info">
	<p>
		<?php
		printf(
			// translators: %1$s Current user's name, %2$s Plugin name.
			esc_html__( 'Hey %1$s, I noticed you\'ve been using %2$s for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.', '404-to-301' ),
			esc_html( $name ),
			'<strong>404 to 301</strong>'
		);
		?>
	</p>
	<p>
		<a href="https://wordpress.org/support/plugin/404-to-301/reviews/#new-post" target="_blank">
			<?php esc_html_e( 'Ok, you deserve it', '404-to-301' ); ?>
		</a>
	</p>
	<p>
		<a href="<?php echo esc_url( add_query_arg( 'dd404_wporg_review', 'later' ) ); ?>">
			<?php esc_html_e( 'Nope, maybe later', '404-to-301' ); ?>
		</a>
	</p>
	<p>
		<a href="<?php echo esc_url( add_query_arg( 'dd404_wporg_review', 'dismiss' ) ); ?>">
			<?php esc_html_e( 'I already did', '404-to-301' ); ?>
		</a>
	</p>
</div>
