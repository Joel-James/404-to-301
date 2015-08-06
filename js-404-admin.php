<?php
/*
* Admin setting form
* Using post values
*/
	if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
    if(isset($_POST['js_hidden']) && $_POST['js_hidden'] == 'Y') {
        $type = $_POST['type'];
        update_option('type', $type);
		
		$link = $_POST['link'];
        update_option('link', $link);

        ?>
        <div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
        <?php
    } else {
        $type = get_option('type');
        $link = get_option('link');
    }
?>
<div class="wrap">
<table width="100%">
<tr><td width="70%">
    <?php    echo "<h3>" . __( '404 to 301', 'oscimp_trdom' ) . " <a href='http://www.joelsays.com/plugins/404-to-301/' target='_blank'>Plugin Website</a></h3>"; ?>
    <hr/>
	<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="js_hidden" value="Y">
        <?php    echo "<h4>" . __( 'Redirect Type Settings', 'oscimp_trdom' ) . "</h4>"; ?>
        <p><?php _e("Type of redirect : " ); ?><select name='type' id='type'>
		<option value='301' <?php if($type=='301'){echo 'selected';}?>>301 Permanent</option>
		<option value='302' <?php if($type=='302'){echo 'selected';}?>>302 Temporary</option>
		<option value='307' <?php if($type=='307'){echo 'selected';}?>>307 Temporary</option>
		</select></p>
		<hr />
		<?php    echo "<h4>" . __( 'Redirect Page Settings', 'oscimp_trdom' ) . "</h4>"; ?>
        <p><?php _e("Redirect to : " ); ?><input type="text" id="link" name="link" placeholder="http://example.com" value="<?php echo $link; ?>">    
		<p>Please make sure to add http:// to the url
        <p class="submit">
        <input type="submit" name="Submit" id="submit" class="button button-primary" value="<?php _e('Update Options', 'oscimp_trdom' ) ?>" />
        </p>
    </form>
</td><td width="30%">
<div align="center"><a style="href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=XUVWY8HUBUXY4" target="_blank"><img src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif"></a></div><br/>
<h4>If you think my plugin is useful, please consider a small donation.</h4>
<h3>Feel free to use <a href="http://www.joelsays.com/members-area/support/plugin-support-404-to-301/" target="_blank">Support Forum </a>if you have any doubts or feedback</h4>
<h4>Please <a href="https://wordpress.org/support/view/plugin-reviews/404-to-301?filter=5#postform" target="_blank">add a review/rating</a> for this plugin @WordPress</h4></td>
</tr></table></div>
<br/><br><hr/>
<div>
<h4>Main advantage of this plugin is that 404 errors in your website will not be considered as error pages.<br/>Automatically creates a 301 permanent redirect if a 404 link found to your website.</h4><br/><h3>Check <a href='https://support.google.com/webmasters/answer/93633?hl=en' target='_blank'>Official Google Page</a> to know more about 301 redirect</h3>
</div>