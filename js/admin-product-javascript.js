'use strict';
jQuery(document).ready(function () {
    jQuery('#wapi_delete_local').on('click', function () {
        var $post_id = jQuery(this).attr('data-wapi_post_id');
        if (confirm('Delete local Product Info settings???')) {
            jQuery.ajax({
                url: "admin-ajax.php?action=wapi_save_settings",
                type: 'post',
                data: {
                    wapi_post_id: $post_id
                },
                success: function () {
                    location.reload();
                }
            });
        }
    });
});

