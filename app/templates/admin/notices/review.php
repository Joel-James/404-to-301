<?php

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

/**
 * Provide a view for the review request notice.
 *
 * @va \WP_User $current_user Current user object.
 *
 * @link   https://duckdev.com
 * @since  4.0
 *
 * @author Joel James <me@joelsays.com>
 */
?>
<div class="notice notice-success">
    <p>
		<?php printf( __( 'Hey %1$s, I noticed you\'ve been using %2$s for more than 1 week – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.', '404-to-301' ),
			empty( $current_user->display_name ) ? __( 'there', '404-to-301' ) : ucwords( $current_user->display_name ),
			'<strong>404 to 301</strong>'
		); ?>
    </p>
    <p>
        <a href="https://wordpress.org/support/plugin/404-to-301/reviews/#new-post" target="_blank"><?php esc_html_e( 'Ok, you deserve it', '404-to-301' ); ?></a>
    </p>
    <p>
        <a href="<?php echo add_query_arg( 'review_action', 'later' ); // later. ?>"><?php esc_html_e( 'Nope, maybe later', '404-to-301' ); ?></a>
    </p>
    <p>
        <a href="<?php echo add_query_arg( 'review_action', 'dismiss' ); // dismiss link. ?>"><?php esc_html_e( 'I already did', '404-to-301' ); ?></a>
    </p>
</div>