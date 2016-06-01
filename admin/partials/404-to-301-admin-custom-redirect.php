<div id="i4t3-redirect-modal" style="display:none;">
    <div id="i4t3-thickbox-content" class="wrap">
        <form id="i4t3_custom_redirect_form" action="javascript:void(0)">
            <table class="form-table">
                <tr>
                    <th><?php _e('Redirecting from', '404-to-301'); ?> :</th>
                    <td><strong><p id="i4t3_redirect_404_text">/404-not-found</p></strong></td>
                </tr>
                <tr>
                    <th><?php _e('Redirect to', '404-to-301'); ?> :</th>
                    <td>
                        <input type="text" size="40" name="i4t3_custom_redirect" id="i4t3_redirect_url" value="">
                        <p class="description"><?php _e('Enter the url if you want to set custom redirect for above 404 path. Enter the full url including http://. Leave empty if you want to follow deafult settings', '404-to-301'); ?>.</p>
                        <input type="hidden" value="" id="i4t3_redirect_404">
                        <input type="hidden" value="<?php echo wp_create_nonce( "i4t3_custom_redirect_nonce" ); ?>" id="i4t3_custom_redirect_nonce">
                    </td>
                </tr>
                <tr>
                    <td><span class="spinner i4t3-spinner"></span></td>
                    <td>
                        <?php
                            submit_button(
                                __('Save Redirect', '404-to-301'),
                                'primary',
                                'i4t3_custom_redirect_submit',
                                false,
                                array(
                                    'id' => 'i4t3_custom_redirect_submit'
                                )
                            );
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>