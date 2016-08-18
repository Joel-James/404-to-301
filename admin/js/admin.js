(function($) {
    'use strict';

    /**
     * All of the code for our admin-specific JavaScript source
     * should reside in this file.
     *
     * Note that this assume you're going to use jQuery, so it prepares
     * the $ function reference to be used within the scope of this
     * function.
     *
     * From here, we are able to define handlers for when the DOM is
     * ready:
     *
     * $(function() {
     *
     * });
     *
     * Or when the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and so on.
     */
    $(function() {
        $('#i4t3_redirect_to').change(function() {
            var redirect_to = $(this).val();
            if(redirect_to == 'page') {
                $('#custom_page').show();
                $('#custom_url').hide();
            } else if(redirect_to == 'link') {
                $('#custom_url').show();
                $('#custom_page').hide();
            } else if(redirect_to == 'none') {
                $('#custom_page').hide();
                $('#custom_url').hide();
            }
        });
        // open custom redirect form modal
        $('.i4t3_redirect_thickbox').on('click', function() {
            var data = {
                'action': 'i4t3_redirect_thickbox',
                'url_404': $(this).attr('url_404')
            };

            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajaxurl, data, function(response) {
                tb_show(i4t3strings.redirect,"#TB_inline?width=700&height=300&inlineId=i4t3-redirect-modal");
                $('#i4t3_redirect_404').val(response.url_404);
                $('#i4t3_redirect_404_text').html(response.url_404);
                $('#i4t3_redirect_url').val(response.url);
            });
        });
        // save custom redirect value
        $('#i4t3_custom_redirect_submit').on('click', function() {
            $(this).addClass('disabled');
            $('.i4t3-spinner').css('visibility', 'visible');
            var data = {
                'action': 'i4t3_redirect_form',
                'url_404': $('#i4t3_redirect_404').val(),
                'url': $('#i4t3_redirect_url').val(),
                'nonce': $('#i4t3_custom_redirect_nonce').val()
            };
            // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
            $.post(ajaxurl, data, function(response) {
                // close the modal
                tb_remove();
                $('#i4t3_custom_redirect_submit').removeClass('disabled');
                $('.i4t3-spinner').css('visibility', 'hidden');
            });
        });
    })
})(jQuery);