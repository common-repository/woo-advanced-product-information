jQuery(document).ready(function () {
//sale countdown timer
	if(jQuery('.wapi-instock') && jQuery('.in-stock')){
		jQuery('.in-stock').hide();
	}
	if(document.getElementById('wapi-countdown-end')) {
		jQuery('.wapi-countdown').show();
		var countDownDate = document.getElementById('wapi-countdown-end').value * 1000;
		// Update the count down every 1 second
		var x = setInterval(function () {
			var now = new Date().getTime();
			var distance = countDownDate - now;
			var days = Math.floor(distance / (1000 * 60 * 60 * 24));
			var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
			var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
			var seconds = Math.floor((distance % (1000 * 60)) / 1000);
			document.getElementById("wapi-countdown-time").innerHTML = days + "d " + hours + "h "
				+ minutes + "m " + seconds + "s ";
			if (distance < 0) {
				clearInterval(x);
				document.getElementById("wapi-countdown-time").innerHTML = "";
				jQuery('.wapi-countdown').hide();
			}
		}, 1000);
	}

	jQuery('.wapi-coupon-text').on('click',function () {
		jQuery('.wapi-coupon-description').toggle();
		jQuery('.wapi-coupon-arrow').toggleClass('arrow_triangle-up');
        jQuery('.wapi-coupon-arrow').toggleClass('arrow_triangle-down');
    })

});