'use strict';
jQuery(document).ready(function () {
    jQuery('.vi-ui.menu .item').vi_tab({
        history: true,
        historyType: 'hash'
    });

    /*Search*/
    jQuery(".search-product").select2({
        closeOnSelect: false,
        placeholder       : "Please fill in your  product title",
        ajax              : {
            url           : "admin-ajax.php?action=wapi_search_product",
            dataType      : 'json',
            type          : "GET",
            quietMillis   : 50,
            delay         : 250,
            data          : function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache         : true
        },
        escapeMarkup      : function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    /*Search*/
    jQuery(".search-category").select2({
        closeOnSelect: false,
        placeholder       : "Please fill in your category title",
        ajax              : {
            url           : "admin-ajax.php?action=wapi_search_cate",
            dataType      : 'json',
            type          : "GET",
            quietMillis   : 50,
            delay         : 250,
            data          : function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache         : true
        },
        escapeMarkup      : function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    jQuery(".coupon-search").select2({
        placeholder: "Type coupon code here",
        ajax: {
            url: "admin-ajax.php?action=wapi_search_coupon",
            dataType: 'json',
            type: "GET",
            quietMillis: 50,
            delay: 250,
            data: function (params) {
                return {
                    keyword: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        },
        escapeMarkup: function (markup) {
            return markup;
        }, // let our custom formatter work
        minimumInputLength: 1
    });
    jQuery('#add-row').on('click', function () {
        jQuery('.wapi-icons-wrap').hide();
        jQuery('#custom-table').append('<tr class="row-edit"><td><input type="hidden" name="custom_row_icon[]" class="custom-row-icon" value=""/><span class=""></span><a href="javascrip:void(0);" class="vi-ui button choose-icon">Choose an icon</a></td><td><input type="text" name="custom_row_heading[]" ></td><td><input type="text" name="custom_row_text[]" ></td><td><input type="text" name="custom_row_url[]"></td><td><a href="javascrip:void(0);" class="vi-ui negative button delete-row">Delete</a> </td></tr>');
        jQuery('.delete-row').on('click', function () {
            if(confirm('Delete this item?')){
                jQuery(this).parent().parent().html('');
            }
        });
        //select icon
        jQuery('.choose-icon').on('click', function () {
            jQuery('.row-edit').removeClass('selected-row');
            jQuery(this).parent().parent().addClass('selected-row');
            jQuery('.wapi-icons-wrap').show();
            jQuery('.wapi-overlay').show();
            var $old = jQuery(this).parent().find('span').prop('class');
            var $selected = '';

        });
        jQuery('.icon-wrap').find('span').on('click', function () {
            jQuery('.icon-wrap').find('span').css({'background-color': 'white'});
            jQuery(this).css({'background-color': 'lightblue'});
            jQuery('.selected-icon-class').val(jQuery(this).prop('class'));
            jQuery('.selected-icon-id').val(jQuery(this).data()['icon_id']);
        });
        jQuery('.select-icon-cancel').on('click', function () {
            jQuery('.wapi-icons-wrap').hide();
            jQuery('.wapi-overlay').hide();
            jQuery('.row-edit').removeClass('selected-row');
        });
        jQuery('.select-icon-ok').on('click', function () {
            if (jQuery('.selected-icon-id').val() !== '') {
                jQuery('.selected-row').find('span').prop('class', jQuery('.selected-icon-class').val());
                jQuery('.selected-row').find('.custom-row-icon').val(jQuery('.selected-icon-id').val());
            }
            jQuery('.wapi-icons-wrap').hide();
            jQuery('.wapi-overlay').hide();
            jQuery('.row-edit').removeClass('selected-row');
        });
    });
    if (jQuery('.delete-row')) {
        jQuery('.delete-row').on('click', function () {
            if(confirm('Delete this item?')){
                jQuery(this).parent().parent().html('');
            }
        });
    }

    jQuery('.choose-icon').on('click', function () {
        jQuery('.row-edit').removeClass('selected-row');
        jQuery(this).parent().parent().addClass('selected-row');
        jQuery('.wapi-icons-wrap').show();
        jQuery('.wapi-overlay').show();
        var $old = jQuery(this).parent().find('span').prop('class');
        var $selected = '';
    });
    jQuery('.icon-wrap').find('span').on('click', function () {
        jQuery('.icon-wrap').find('span').css({'background-color': 'white'});
        jQuery(this).css({'background-color': 'lightblue'});
        jQuery('.selected-icon-class').val(jQuery(this).prop('class'));
        jQuery('.selected-icon-id').val(jQuery(this).data()['icon_id']);
    });

    jQuery('.select-icon-cancel').on('click', function () {
        jQuery('.wapi-icons-wrap').hide();
        jQuery('.wapi-overlay').hide();
        jQuery('.row-edit').removeClass('selected-row');
    });
    jQuery('.select-icon-ok').on('click', function () {
        if (jQuery('.selected-icon-id').val() !== '') {
            jQuery('.selected-row').find('span').prop('class', jQuery('.selected-icon-class').val());
            jQuery('.selected-row').find('.custom-row-icon').val(jQuery('.selected-icon-id').val());
        }
        jQuery('.wapi-icons-wrap').hide();
        jQuery('.wapi-overlay').hide();
        jQuery('.row-edit').removeClass('selected-row');
    });
    jQuery('.icon-wrap').find('span').on('dblclick', function () {
        jQuery('.icon-wrap').unbind();
        jQuery('.selected-row').find('span').prop('class', jQuery(this).prop('class'));
        jQuery('.selected-row').find('.custom-row-icon').val(jQuery(this).data()['icon_id']);
        jQuery('.wapi-icons-wrap').hide();
        jQuery('.wapi-overlay').hide();
        jQuery('.row-edit').removeClass('selected-row');
    });
    jQuery('#wapi_update_product').on('click', function () {
        return confirm('If OK, the local Product Info settings of each product will be set to this global settings.');
    });
    jQuery('#wapi_delete_option').on('click', function () {
        return confirm('Reset Product Info settings to default???');
    });

    /*Color picker*/
    jQuery('.color-picker').iris({
        change: function (event, ui) {
            jQuery(this).parent().find('.color-picker').css({backgroundColor: ui.color.toString()});
        },
        hide: true,
        border: true
    }).click(function () {
        jQuery('.iris-picker').hide();
        jQuery(this).closest('td').find('.iris-picker').show();
    });

    jQuery('body').click(function () {
        jQuery('.iris-picker').hide();
    });
    jQuery('.color-picker').click(function (event) {
        event.stopPropagation();
    });


    /*Color picker*/
    jQuery('.wapi-shortcode-prop-wrap-right-color .color-picker').iris({
        change: function (event, ui) {
            jQuery(this).parent().find('.color-picker').css({backgroundColor: ui.color.toString()});
            var ele = jQuery(this).data('ele');
            jQuery('.wapi-icons').find('.icon-wrap span').css({'color': ui.color.toString()});
        },
        hide: true,
        border: true
    }).click(function () {
        jQuery('.iris-picker').hide();
        jQuery('.wapi-shortcode-prop-wrap-right-color .iris-picker').show();
    });

    jQuery('.payment-active-check').on('click', function () {
        if (jQuery(this).prop('checked') === true) {
            jQuery(this).parent().find('.payment-active').val('1');
        } else {
            jQuery(this).parent().find('.payment-active').val('');
        }
    });
    jQuery('.ui-sortable').sortable();

//    new select icons
    jQuery('.wapi-select-icon-button').on('click', function () {
        jQuery('.wapi-icons-wrap').show();
        jQuery('.wapi-overlay').show();
        selectIcons(jQuery(this));
    });
    jQuery('.wapi-overlay').on('click',function () {
        jQuery(this).hide();
        jQuery('.wapi-icons-wrap').hide();
    });
    function selectIcons(button) {
        var input = button.parent().find('input');
        var caretPos = input[0].selectionStart;
        var textAreaTxt = input.val();
        var shortcode ='[wapi_icon {id} {color} {size}]';
        var iconId='', iconColor='', iconSize='';
        jQuery('.select-icon-ok').on('click',function () {
            iconId = jQuery('.selected-icon-id').val();
            iconColor = jQuery('.wapi-shortcode-prop-wrap-right-color .color-picker').val();
            iconSize = jQuery('.wapi-shortcode-icon-size').val();
            if(iconId){
                shortcode=shortcode.replace('{id}','id="'+iconId+'"');
                shortcode=shortcode.replace('{color}','color="'+iconColor+'"');
                shortcode=shortcode.replace('{size}','size="'+iconSize+'"');
                input.val(textAreaTxt.substring(0, caretPos) + shortcode + textAreaTxt.substring(caretPos));
            }
            jQuery('.wapi-icons-wrap').hide();
            jQuery('.wapi-overlay').hide();
        });
        jQuery('.icon-wrap').find('span').on('dblclick', function () {
            jQuery('.icon-wrap').unbind();
            iconId = jQuery('.selected-icon-id').val();
            iconColor = jQuery('.wapi-shortcode-prop-wrap-right-color .color-picker').val();
            iconSize = jQuery('.wapi-shortcode-icon-size').val();
            if(iconId){
                shortcode=shortcode.replace('{id}','id="'+iconId+'"');
                shortcode=shortcode.replace('{color}','color="'+iconColor+'"');
                shortcode=shortcode.replace('{size}','size="'+iconSize+'"');
                input.val(textAreaTxt.substring(0, caretPos) + shortcode + textAreaTxt.substring(caretPos));
            }
            jQuery('.wapi-icons-wrap').hide();
            jQuery('.wapi-overlay').hide();
        });
    }
    jQuery('.wapi-shortcode-prop-wrap-right-size input').on('change',function(){
        jQuery('.wapi-icons').find('.icon-wrap span').css({'font-size': jQuery(this).val()+'px'});
    });
    jQuery('input[name="submit"]').on('click',function () {
        for(var i=0;i<jQuery('.custom-row-icon').length;i++){
            if(jQuery('input[name="custom-row-icon[]"]').eq(i).val()==='' ){
                alert('Please select icon');
                return false;
            }
            if( jQuery('input[name="custom-row-text[]"]').eq(i).val()===''){
                alert('Content can not be empty');
                jQuery('input[name="custom-row-text[]"]').eq(i).focus();
                return false;
            }
        }
    });
//    stock
    jQuery('#instock-style').on('change',function(){
        if(jQuery(this).val()==1){
            jQuery('.instock-count-bar-style').addClass('instock-count-bar-style-hide');
        }else{
            jQuery('.instock-count-bar-style').removeClass('instock-count-bar-style-hide');
        }
    });
    jQuery('.wapi-checkbox-enable').on('click',function(){
        var datatab=jQuery(this).parent().parent().parent().parent().parent().parent().data()['tab'];
        if(jQuery(this).prop('checked')==true){
            jQuery('a.item[data-tab="'+datatab+'"]').removeClass('wapi-inactive-item');
        }else{
            jQuery('a.item[data-tab="'+datatab+'"]').addClass('wapi-inactive-item');
        }
    })
});

