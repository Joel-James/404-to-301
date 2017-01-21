<?php include_once JJ4T3_DIR . 'includes/functions/jj4t3-debug-functions.php'; ?>
<div id="dashboard-widgets-wrap">
    <div id="dashboard-widgets" class="metabox-holder">
		<div id="postbox-container-1" class="postbox-container">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox ">
						<h2 class="hndle ui-sortable-handle jj4t3-handle"><span class="dashicons dashicons-info"></span> <?php _e( 'Plugin Information', JJ4T3_DOMAIN ); ?></h2>
						<div class="inside">
							<div class="main">
								<ul>
									<li><label><?php _e( 'Name', JJ4T3_DOMAIN ); ?> : </label><strong><?php _e( '404 to 301', JJ4T3_DOMAIN ); ?></strong></li>
									<li><label><?php _e( 'Version', JJ4T3_DOMAIN ); ?> : v<?php echo JJ4T3_VERSION; ?></label></li>
									<li><label><?php _e( 'Author', JJ4T3_DOMAIN ); ?> : <strong><a href="https://duckdev.com/" class="i4t3-author-link" target="_blank" title="<?php _e( 'Visit author website', JJ4T3_DOMAIN ); ?>">Joel James</a></strong></label></li>
									<li><label><a href="https://duckdev.com/support/docs/category/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e( 'Visit plugin documentation', JJ4T3_DOMAIN ); ?>">> <strong><?php _e( 'Plugin documentation', JJ4T3_DOMAIN ); ?></strong></a></label></li>
									<li><label><a href="https://duckdev.com/products/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e( 'Visit plugin website', JJ4T3_DOMAIN ); ?>"><strong>> <?php _e( 'More details about the plugin', JJ4T3_DOMAIN ); ?></strong></a></label></li>
									<li><label><?php _e( 'Need help?', JJ4T3_DOMAIN ); ?></label> <strong><a href="https://wordpress.org/support/plugin/404-to-301"><?php _e( 'Create a ticket', JJ4T3_DOMAIN ); ?></a></strong></li>
								</ul>
							</div>
						</div>
					</div>
					<div class="postbox ">
						<h2 class="hndle ui-sortable-handle jj4t3-handle"><span class="dashicons dashicons-smiley"></span> <?php _e( 'Like the plugin?', JJ4T3_DOMAIN ); ?></h2>
						<div class="inside">
							<div class="main">
								<ul>
									<li><label><span class="dashicons dashicons-star-filled"></span> <strong><a href="https://wordpress.org/support/plugin/404-to-301/reviews/?filter=5#new-post" target="_blank" title="<?php _e( 'Rate now', JJ4T3_DOMAIN ); ?>"><?php _e( 'Rate this on WordPress', JJ4T3_DOMAIN ); ?></a></strong></label></li>
									<li><label><span class="dashicons dashicons-heart"></span> <strong><a href="https://www.paypal.me/JoelCJ" target="_blank" title="<?php _e( 'Donate now', JJ4T3_DOMAIN ); ?>"><?php _e( 'Make a small donation', JJ4T3_DOMAIN ); ?></a></strong></label></li>
									<li><label><span class="dashicons dashicons-admin-plugins"></span> <strong><a href="https://github.com/joel-james/404-to-301/" target="_blank" title="<?php _e( 'Contribute now', JJ4T3_DOMAIN ); ?>"><?php _e( 'Contribute to the Plugin', JJ4T3_DOMAIN ); ?></a></strong></label></li>
									<li><label><span class="dashicons dashicons-twitter"></span> <strong><a href="https://twitter.com/intent/tweet?text=I am using 404 to 301 plugin by @Joel_James to handle all 404 errors in my @WordPress site - it is awesome! > https://wordpress.org/plugins/404-to-301/&source=webclient" target="_blank" title="<?php _e( 'Tweet now', JJ4T3_DOMAIN ); ?>"><?php _e( 'Tweet about the Plugin', JJ4T3_DOMAIN ); ?></a></strong></label></li>
							</div>
						</div>
					</div>
					<div class="postbox ">
						<h2 class="hndle ui-sortable-handle jj4t3-handle"><span class="dashicons dashicons-businessman"></span> <?php _e( 'About the Developer', JJ4T3_DOMAIN ); ?></h2>
						<div class="inside">
							<div class="main">
								<?php $current_user = wp_get_current_user(); ?>
								<?php $uname = ( $current_user->user_firstname == '' ) ? ucfirst( $current_user->user_login ) : $current_user->user_firstname; ?>
								<p><?php printf( __( 'Yo %s.', JJ4T3_DOMAIN ), '<strong>' . $uname . '</strong>'); ?> <?php _e('Thank you for using 404 to 301', JJ4T3_DOMAIN ); ?></p>
								<p><?php _e( 'This plugin was developed and is maintained by <strong>Joel James</strong>, from God\'s own country - Kerala, India.', JJ4T3_DOMAIN ); ?></p>
							</div>
						</div>
					</div>
				</div>
		</div>
		<div id="postbox-container-2" class="postbox-container">
				<div class="meta-box-sortables ui-sortable">
					<div class="postbox ">
						<h2 class="hndle ui-sortable-handle jj4t3-handle"><span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Log Manager Addon', JJ4T3_DOMAIN ); ?></h2>
						<div class="inside">
							<div class="main">
								<p><?php _e( 'Error Log Manager addon is available for 404 to 301 now. Make 404 error management more easy.', JJ4T3_DOMAIN ); ?></p>
								<p><span class="dashicons dashicons-backup"></span> <?php _e( 'Instead of email alerts on every error, get Hourly, Daily, Twice a day, Weekly, Twice a week email alerts.', JJ4T3_DOMAIN ); ?></p>
								<p><span class="dashicons dashicons-trash"></span> <?php _e( 'Automatically clear old error logs after few days to reduce db load.', JJ4T3_DOMAIN ); ?></p>
								<p><a class="i4t3-author-link" href="https://duckdev.com/products/404-to-301-log-manager/" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e( 'See more details', JJ4T3_DOMAIN ); ?></a></p>
							</div>
						</div>
					</div>
				</div>
		</div>
        <div id="postbox-container-3" class="postbox-container" style="width: 50%;">
            <div class="meta-box-sortables ui-sortable">
                <div class="postbox ">
                    <h2 class="hndle ui-sortable-handle jj4t3-handle"><span class="dashicons dashicons-hammer"></span> <?php _e( 'Debug Data', JJ4T3_DOMAIN ); ?></h2>
                    <div class="inside">
                        <div class="main">
							<textarea readonly="readonly" onclick="this.focus(); this.select()" id="system-info-textarea"><?php echo jj4t3_get_sysinfo(); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>