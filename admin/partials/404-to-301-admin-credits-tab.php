<div class="wrap">
<?php
if( get_option( 'i4t3_agreement', 2 ) == 2 ) {
    include_once '404-to-301-admin-agreement-tab.php';
}
?>
    <br>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">
            <div id="post-body-content">
                <div class="postbox">
                    <h3 class="hndle"><span><?php _e('About the plugin & developer', '404-to-301'); ?></span></h3>
                    <div class="inside">
                        <div class="c4p-clearfix">
                            <div class="c4p-left">
                                <img src="<?php echo I4T3_PATH . 'admin/images/foxe.png'; ?>" class="c4p-author-image" />
                            </div>
                            <div class="c4p-left" style="width: 70%">
                                <?php $uname = ( $current_user->user_firstname == '' ) ? $current_user->user_login : $current_user->user_firstname; ?>
                                <p><?php printf(__('Yo %s!', '404-to-301'), '<strong>' . $uname . '</strong>'); ?> <?php _e('Thank you for using 404 to 301', '404-to-301'); ?></p>
                                <p>
                                    <?php _e('This plugin is brought to you by', '404-to-301'); ?> <a href="https://thefoxe.com/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit author website', '404-to-301'); ?>"><strong>The Foxe</strong></a>, <?php _e('a web store developed and managed by Joel James.', '404-to-301'); ?>
                                </p>
                                <p>
                                <hr/>
                                </p>
                                <p>
                                    <?php _e('So you installed this plugin and how is it doing? Feel free to', '404-to-301'); ?> <a href="https://thefoxe.com/contact/" class="i4t3-author-link" target="_blank" title="<?php _e('Contact the developer', '404-to-301'); ?>"><?php _e('get in touch with me', '404-to-301'); ?></a> <?php _e('anytime for help. I am always happy to help.', '404-to-301'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle"><span><?php _e('Debugging Data', '404-to-301'); ?></span></h3>
                    <div class="inside">
                        <div class="c4p-clearfix">
                            <div class="c4p-left" style="width: 70%">
                                <?php echo _404_To_301_Admin::i4t3_get_debug_data(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="postbox-container-1" class="postbox-container">

                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-info"></span> <?php _e('Plugin Information', '404-to-301'); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <label><?php _e('Name', '404-to-301'); ?> : </label>
                            <span><strong><?php _e('404 to 301', '404-to-301'); ?></strong></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Version', '404-to-301'); ?> : v<?php echo I4T3_VERSION; ?></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Author', '404-to-301'); ?> : <a href="https://thefoxe.com/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit author website', '404-to-301'); ?>">The Foxe</a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><a href="https://thefoxe.com/docs/docs/category/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit plugin website', '404-to-301'); ?>"><strong><?php _e('Plugin documentation', '404-to-301'); ?></strong></a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><a href="https://thefoxe.com/products/404-to-301/" class="i4t3-author-link" target="_blank" title="<?php _e('Visit plugin website', '404-to-301'); ?>"><strong><?php _e('More details about the plugin', '404-to-301'); ?></strong></a></label>
                            <span></span>
                        </div>
                        <div class="misc-pub-section">
                            <label><?php _e('Need help?', '404-to-301'); ?></label>
                            <span><strong><a href="https://thefoxe.com/contact/"><?php _e('Contact support', '404-to-301'); ?></a></strong></span>
                        </div>
                        <div class="misc-pub-section">
                            <?php if( get_option( 'i4t3_agreement', 0 ) == 1 ) { ?>
                            <a class="button-secondary" href="<?php echo I4T3_HELP_PAGE; ?>&i4t3_agreement=0" id="i4t3-hide-admin-notice"><?php _e('Disable UAN', '404-to-301'); ?></a>
                            <?php } else { ?>
                            <a class="button-primary" href="<?php echo I4T3_HELP_PAGE; ?>&i4t3_agreement=1" id="i4t3-accept-terms"><?php _e('Enable UAN', '404-to-301'); ?></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-admin-plugins"></span> <?php _e('Log Manager Addon', '404-to-301'); ?></h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <p><?php _e('Error Log Manager addon is available for 404 to 301 now. Make 404 error management more easy.', '404-to-301'); ?></p>
                            <p><span class="dashicons dashicons-backup"></span> <?php _e('Instead of email alerts on every error, get Hourly, Daily, Twice a day, Weekly, Twice a week email alerts.', '404-to-301'); ?></p>
                            <p><span class="dashicons dashicons-trash"></span> <?php _e('Automatically clear old error logs after few days to reduce db load.', '404-to-301'); ?></p>
                            <p><a class="i4t3-author-link" href="https://thefoxe.com/products/404-to-301-log-manager/" target="_blank"><span class="dashicons dashicons-external"></span> <?php _e('See More Details', '404-to-301'); ?></a></p>
                        </div>
                    </div>
                </div>
                <div class="postbox">
                    <h3 class="hndle ui-sortable-handle"><span class="dashicons dashicons-smiley"></span> <?php _e('Like the plugin', '404-to-301'); ?>?</h3>
                    <div class="inside">
                        <div class="misc-pub-section">
                            <span class="dashicons dashicons-star-filled"></span> <label><strong><a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform" target="_blank" title="<?php _e('Rate now', '404-to-301'); ?>"><?php _e('Rate this on WordPress', '404-to-301'); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-heart"></span> <strong><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4" target="_blank" title="<?php _e('Donate now', '404-to-301'); ?>"><?php _e('Make a small donation', '404-to-301'); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-admin-plugins"></span> <strong><a href="https://github.com/joel-james/404-to-301/" target="_blank" title="<?php _e('Contribute now', '404-to-301'); ?>"><?php _e('Contribute to the Plugin', '404-to-301'); ?></a></strong></label>
                        </div>
                        <div class="misc-pub-section">
                            <label><span class="dashicons dashicons-twitter"></span> <strong><a href="https://twitter.com/home?status=I%20am%20using%20404%20to%20301%20plugin%20by%20%40Joel_James%20to%20handle%20all%20404%20errors%20in%20my%20%40WordPress%20site%20-%20it%20is%20awesome!%20%3E%20https://wordpress.org/plugins/404-to-301/" target="_blank" title="<?php _e('Tweet now', '404-to-301'); ?>"><?php _e('Tweet about the Plugin', '404-to-301'); ?></a></strong></label>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
