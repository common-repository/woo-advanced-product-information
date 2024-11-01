'use strict';
jQuery(document).ready(function () {
    jQuery(document).ready(function () {
//sale countdown timer
        var distance, days, hours, minutes, seconds, i;
        // Update the count down every 1 second
        var wooCountdown = jQuery('.wapinfo-shortcode-wrap-wrap');
        distance = jQuery('.wapinfo-shortcode-data-end_time').map(function () {
            return parseInt(jQuery(this).val());
        });
        var x = setInterval(function () {
            for (i = 0; i < wooCountdown.length; i++) {
                days = Math.floor(distance[i] / 86400);
                hours = Math.floor((distance[i] % (86400)) / (3600));
                minutes = Math.floor((distance[i] % (3600)) / (60));
                seconds = Math.floor((distance[i] % (60)));
                if (days < 100) {
                    days = ("0" + days).slice(-2);
                    if (days == 0) {
                        jQuery('.wapinfo-shortcode-countdown-date').eq(i).hide();
                        jQuery('.wapinfo-shortcode-wrap-wrap').eq(i).find('.wapinfo-shortcode-countdown-time-separator').eq(0).hide();
                    }
                }
                jQuery('.wapinfo-shortcode-countdown-date-value').eq(i).html(days);
                jQuery('.wapinfo-shortcode-countdown-hour-value').eq(i).html(("0" + hours).slice(-2));
                jQuery('.wapinfo-shortcode-countdown-minute-value').eq(i).html(("0" + minutes).slice(-2));
                jQuery('.wapinfo-shortcode-countdown-second-value').eq(i).html(("0" + seconds).slice(-2));
                distance[i]--;
                if (distance[i] < 0) {
                    clearInterval(x);
                    window.location.href = window.location.href;
                }
            }
        }, 1000);
    });

});