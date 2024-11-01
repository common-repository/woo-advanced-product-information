<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */

if ( ! function_exists( 'vi_include_folder' ) ) {
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {

		/*Include all files in payment folder*/
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( '.' != $sfile && '..' != $sfile ) {
				if ( is_file( $path . "/" . $sfile ) ) {
					$ext_file  = pathinfo( $path . "/" . $sfile );
					$file_name = $ext_file['filename'];
					if ( $ext_file['extension'] ) {
						if ( in_array( $ext_file['extension'], $ext, true ) ) {
							$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );

							if ( ! class_exists( $class ) ) {
								require_once $path . $sfile;
								if ( class_exists( $class ) ) {

									new $class;
								}
							}
						}
					}
				}
			}
		}
	}
}
if ( ! function_exists( 'wapi_select_icon' ) ) {
	function wapi_select_icon() {
		if ( ! get_option( '_wapi_settings' ) ) {
			return;
		}
		?>
        <div class="wapi-icons">
			<?php
			$icons = wapi_get_icon();
			foreach ( $icons as $k => $m ) {
				?>
                <div class="icon-wrap  wapi-custom-icons" title="<?php echo esc_attr( $k ) ?>">
                    <span class="<?php echo esc_attr( $m ) ?>" data-icon_id="<?php echo esc_attr( $k ) ?>"></span></div>
				<?php
			}
			?>
        </div>
        <div class="wapi-shortcode-prop-wrap">
            <div class="wapi-shortcode-prop-wrap-right">
                <div class="wapi-shortcode-prop-wrap-right-color">
                    <span><?php esc_attr_e( 'Color', 'woo-advanced-product-information' ) ?> </span><input type="text"
                                                                                                           class="color-picker"
                                                                                                           placeholder="<?php esc_attr_e( 'Color', 'woo-advanced-product-information' ) ?>">
                </div>
                <div class="wapi-shortcode-prop-wrap-right-size">
                    <span><?php esc_attr_e( 'Size(px)', 'woo-advanced-product-information' ) ?> </span><input
                            type="number" value="22" min="0" max="" class="wapi-shortcode-icon-size"
                            placeholder="<?php esc_html_e( 'Size(px)', 'woo-advanced-product-information' ) ?>">
                </div>
                <input type="hidden" class="selected-icon-id" value="">
                <input type="hidden" class="selected-icon-class" value="">
                <div class="select-icon-ok-wrap">
                    <a class="vi-ui positive button select-icon-ok"><?php esc_html_e( 'Ok', 'woo-advanced-product-information' ); ?></a>
                </div>
                <div class="select-icon-cancel-wrap">
                    <a class="vi-ui negative button select-icon-cancel"><?php esc_html_e( 'Cancel', 'woo-advanced-product-information' ) ?></a>
                </div>
            </div>
        </div>
		<?php
	}

}
if ( ! function_exists( 'wapi_get_params' ) ) {
	function wapi_get_params( $post_id, $params_name ) {
		$return = array();
		if ( get_post_meta( $post_id, '_wapi_settings', true ) ) {
			if ( isset( get_post_meta( $post_id, '_wapi_settings', true )[ $params_name ] ) ) {
				$return = get_post_meta( $post_id, '_wapi_settings', true )[ $params_name ];
			}
		}

		return $return;
	}

}

if ( ! function_exists( 'wapi_get_icon' ) ) {
	function wapi_get_icon( $id = "" ) {
		$icons = array(
			"wapicons-prize-badge-with-star-and-ribbon",
			"wapicons-medal-4",
			"wapicons-signs",
			"wapicons-ribbon-1",
			"wapicons-medal-3",
			"wapicons-badge-2",
			"wapicons-medal-2",
			"wapicons-ribbon",
			"wapicons-badge-1",
			"wapicons-quality-1",
			"wapicons-medal-1",
			"wapicons-quality",
			"wapicons-money-2",
			"wapicons-piggy-bank-3",
			"wapicons-investment",
			"wapicons-coin",
			"wapicons-bitcoin-logo",
			"wapicons-free-shipping-truck",
			"wapicons-free-shipping",
			"wapicons-free",
			"wapicons-chronometer",
			"wapicons-delivery-box-on-a-hand",
			"wapicons-free-delivery-truck",
			"wapicons-international-delivery-business-symbol-of-world-grid-with-an-arrow-around",
			"wapicons-phone-auricular-and-clock-delivery-symbol",
			"wapicons-weight-tool",
			"wapicons-delivery-truck-with-packages-behind",
			"wapicons-talking-by-phone-auricular-symbol-with-speech-bubble",
			"wapicons-woman-with-headset",
			"wapicons-triangular-arrows-sign-for-recycle",
			"wapicons-international-delivery",
			"wapicons-24-hours-delivery",
			"wapicons-call-center-worker-with-headset",
			"wapicons-food",
			"wapicons-ocean-transportation",
			"wapicons-badge",
			"wapicons-shopping-cart-3",
			"wapicons-shopping-cart-2",
			"wapicons-shopping-cart-1",
			"wapicons-shopping-cart",
			"wapicons-checkout",
			"wapicons-transport-8",
			"wapicons-transport-7",
			"wapicons-transport-6",
			"wapicons-transport-5",
			"wapicons-travel-1",
			"wapicons-delivery-truck",
			"wapicons-airplane-shape",
			"wapicons-plane",
			"wapicons-fast-delivery-1",
			"wapicons-fast-delivery",
			"wapicons-transport-4",
			"wapicons-transport-3",
			"wapicons-transport-2",
			"wapicons-transport-1",
			"wapicons-transport",
			"wapicons-startup-2",
			"wapicons-startup-1",
			"wapicons-rocket-icon",
			"wapicons-startup",
			"wapicons-rocket",
			"wapicons-small-rocket-ship-silhouette",
			"wapicons-rocket-ship",
			"wapicons-support-1",
			"wapicons-24-hours-symbol",
			"wapicons-24-hours-phone-service",
			"wapicons-medal",
			"wapicons-ranking-cup",
			"wapicons-rank",
			"wapicons-guarantee",
			"wapicons-podium",
			"wapicons-travel",
			"wapicons-map",
			"wapicons-rating-2",
			"wapicons-rating-1",
			"wapicons-rating",
			"wapicons-review-2",
			"wapicons-good-review",
			"wapicons-review-1",
			"wapicons-review",
			"wapicons-counterclockwide-arrow",
			"wapicons-counterclockwise",
			"wapicons-hourglass-countdown",
			"wapicons-delivery",
			"wapicons-support",
			"wapicons-telephone-line-24-hours-service",
			"wapicons-delivery-man",
			"wapicons-delivery-truck-with-circular-clock",
			"wapicons-logistics-delivery-truck-and-clock",
			"wapicons-logistics-delivery-truck-in-movement",
			"wapicons-world",
			"wapicons-open-eye",
			"wapicons-piggy-bank-2",
			"wapicons-piggy-bank-1",
			"wapicons-piggy-bank-with-dollar-coins",
			"wapicons-piggy-bank",
			"wapicons-compass",
			"wapicons-savings",
			"wapicons-handshake",
			"wapicons-arrows",
			"wapicons-return-of-investment",
			"wapicons-discount-1",
			"wapicons-discount-label-for-commerce",
			"wapicons-coupon-3",
			"wapicons-discount",
			"wapicons-coupon-2",
			"wapicons-coupon-1",
			"wapicons-coupon",
			"wapicons-security-2",
			"wapicons-security-1",
			"wapicons-shield-2",
			"wapicons-security",
			"wapicons-safety",
			"wapicons-shield-1",
			"wapicons-shield",
			"wapicons-amazon-pay-card-logo",
			"wapicons-amazon",
			"wapicons-amazon-logo",
			"wapicons-stripe-logo",
			"wapicons-paypal-logo-1",
			"wapicons-credit-card-payment",
			"wapicons-paypal-logo",
			"wapicons-money-1",
			"wapicons-check",
			"wapicons-credit-card-with-tick",
			"wapicons-secure-payment",
			"wapicons-checked",
			"wapicons-commerce",
			"wapicons-money",
			"wapicons-business",
			"dashicons dashicons-menu",
			"dashicons dashicons-admin-site",
			"dashicons dashicons-dashboard",
			"dashicons dashicons-admin-media",
			"dashicons dashicons-admin-page",
			"dashicons dashicons-admin-comments",
			"dashicons dashicons-admin-appearance",
			"dashicons dashicons-admin-plugins",
			"dashicons dashicons-admin-users",
			"dashicons dashicons-admin-tools",
			"dashicons dashicons-admin-settings",
			"dashicons dashicons-admin-network",
			"dashicons dashicons-admin-generic",
			"dashicons dashicons-admin-home",
			"dashicons dashicons-admin-collapse",
			"dashicons dashicons-filter",
			"dashicons dashicons-admin-customizer",
			"dashicons dashicons-admin-multisite",
			"dashicons dashicons-admin-links",
			"dashicons dashicons-format-links",
			"dashicons dashicons-admin-post",
			"dashicons dashicons-format-standard",
			"dashicons dashicons-format-image",
			"dashicons dashicons-format-gallery",
			"dashicons dashicons-format-audio",
			"dashicons dashicons-format-video",
			"dashicons dashicons-format-chat",
			"dashicons dashicons-format-status",
			"dashicons dashicons-format-aside",
			"dashicons dashicons-format-quote",
			"dashicons dashicons-welcome-write-blog",
			"dashicons dashicons-welcome-edit-page",
			"dashicons dashicons-welcome-add-page",
			"dashicons dashicons-welcome-view-site",
			"dashicons dashicons-welcome-widgets-menus",
			"dashicons dashicons-welcome-comments",
			"dashicons dashicons-welcome-learn-more",
			"dashicons dashicons-image-crop",
			"dashicons dashicons-image-rotate",
			"dashicons dashicons-image-rotate-left",
			"dashicons dashicons-image-rotate-right",
			"dashicons dashicons-image-flip-vertical",
			"dashicons dashicons-image-flip-horizontal",
			"dashicons dashicons-image-filter",
			"dashicons dashicons-undo",
			"dashicons dashicons-redo",
			"dashicons dashicons-editor-bold",
			"dashicons dashicons-editor-italic",
			"dashicons dashicons-editor-ul",
			"dashicons dashicons-editor-ol",
			"dashicons dashicons-editor-quote",
			"dashicons dashicons-editor-alignleft",
			"dashicons dashicons-editor-aligncenter",
			"dashicons dashicons-editor-alignright",
			"dashicons dashicons-editor-insertmore",
			"dashicons dashicons-editor-spellcheck",
			"dashicons dashicons-editor-distractionfree",
			"dashicons dashicons-editor-expand",
			"dashicons dashicons-editor-contract",
			"dashicons dashicons-editor-kitchensink",
			"dashicons dashicons-editor-underline",
			"dashicons dashicons-editor-justify",
			"dashicons dashicons-editor-textcolor",
			"dashicons dashicons-editor-paste-word",
			"dashicons dashicons-editor-paste-text",
			"dashicons dashicons-editor-removeformatting",
			"dashicons dashicons-editor-video",
			"dashicons dashicons-editor-customchar",
			"dashicons dashicons-editor-outdent",
			"dashicons dashicons-editor-indent",
			"dashicons dashicons-editor-help",
			"dashicons dashicons-editor-strikethrough",
			"dashicons dashicons-editor-unlink",
			"dashicons dashicons-editor-rtl",
			"dashicons dashicons-editor-break",
			"dashicons dashicons-editor-code",
			"dashicons dashicons-editor-paragraph",
			"dashicons dashicons-editor-table",
			"dashicons dashicons-align-left",
			"dashicons dashicons-align-right",
			"dashicons dashicons-align-center",
			"dashicons dashicons-align-none",
			"dashicons dashicons-lock",
			"dashicons dashicons-unlock",
			"dashicons dashicons-calendar",
			"dashicons dashicons-calendar-alt",
			"dashicons dashicons-visibility",
			"dashicons dashicons-hidden",
			"dashicons dashicons-post-status",
			"dashicons dashicons-edit",
			"dashicons dashicons-post-trash",
			"dashicons dashicons-trash",
			"dashicons dashicons-sticky",
			"dashicons dashicons-external",
			"dashicons dashicons-arrow-up",
			"dashicons dashicons-arrow-down",
			"dashicons dashicons-arrow-left",
			"dashicons dashicons-arrow-right",
			"dashicons dashicons-arrow-up-alt",
			"dashicons dashicons-arrow-down-alt",
			"dashicons dashicons-arrow-left-alt",
			"dashicons dashicons-arrow-right-alt",
			"dashicons dashicons-arrow-up-alt2",
			"dashicons dashicons-arrow-down-alt2",
			"dashicons dashicons-arrow-left-alt2",
			"dashicons dashicons-arrow-right-alt2",
			"dashicons dashicons-leftright",
			"dashicons dashicons-sort",
			"dashicons dashicons-randomize",
			"dashicons dashicons-list-view",
			"dashicons dashicons-exerpt-view",
			"dashicons dashicons-excerpt-view",
			"dashicons dashicons-grid-view",
			"dashicons dashicons-move",
			"dashicons dashicons-hammer",
			"dashicons dashicons-art",
			"dashicons dashicons-migrate",
			"dashicons dashicons-performance",
			"dashicons dashicons-universal-access",
			"dashicons dashicons-universal-access-alt",
			"dashicons dashicons-tickets",
			"dashicons dashicons-nametag",
			"dashicons dashicons-clipboard",
			"dashicons dashicons-heart",
			"dashicons dashicons-megaphone",
			"dashicons dashicons-schedule",
			"dashicons dashicons-wordpress",
			"dashicons dashicons-wordpress-alt",
			"dashicons dashicons-pressthis",
			"dashicons dashicons-update",
			"dashicons dashicons-screenoptions",
			"dashicons dashicons-cart",
			"dashicons dashicons-feedback",
			"dashicons dashicons-cloud",
			"dashicons dashicons-translation",
			"dashicons dashicons-tag",
			"dashicons dashicons-category",
			"dashicons dashicons-archive",
			"dashicons dashicons-tagcloud",
			"dashicons dashicons-text",
			"dashicons dashicons-media-archive",
			"dashicons dashicons-media-audio",
			"dashicons dashicons-media-code",
			"dashicons dashicons-media-default",
			"dashicons dashicons-media-document",
			"dashicons dashicons-media-interactive",
			"dashicons dashicons-media-spreadsheet",
			"dashicons dashicons-media-text",
			"dashicons dashicons-media-video",
			"dashicons dashicons-playlist-audio",
			"dashicons dashicons-playlist-video",
			"dashicons dashicons-controls-play",
			"dashicons dashicons-controls-pause",
			"dashicons dashicons-controls-forward",
			"dashicons dashicons-controls-skipforward",
			"dashicons dashicons-controls-back",
			"dashicons dashicons-controls-skipback",
			"dashicons dashicons-controls-repeat",
			"dashicons dashicons-controls-volumeon",
			"dashicons dashicons-controls-volumeoff",
			"dashicons dashicons-yes",
			"dashicons dashicons-no",
			"dashicons dashicons-no-alt",
			"dashicons dashicons-plus",
			"dashicons dashicons-plus-alt",
			"dashicons dashicons-plus-alt2",
			"dashicons dashicons-minus",
			"dashicons dashicons-dismiss",
			"dashicons dashicons-marker",
			"dashicons dashicons-star-filled",
			"dashicons dashicons-star-half",
			"dashicons dashicons-star-empty",
			"dashicons dashicons-flag",
			"dashicons dashicons-info",
			"dashicons dashicons-warning",
			"dashicons dashicons-share",
			"dashicons dashicons-share1",
			"dashicons dashicons-share-alt",
			"dashicons dashicons-share-alt2",
			"dashicons dashicons-twitter",
			"dashicons dashicons-rss",
			"dashicons dashicons-email",
			"dashicons dashicons-email-alt",
			"dashicons dashicons-facebook",
			"dashicons dashicons-facebook-alt",
			"dashicons dashicons-networking",
			"dashicons dashicons-googleplus",
			"dashicons dashicons-location",
			"dashicons dashicons-location-alt",
			"dashicons dashicons-camera",
			"dashicons dashicons-images-alt",
			"dashicons dashicons-images-alt2",
			"dashicons dashicons-video-alt",
			"dashicons dashicons-video-alt2",
			"dashicons dashicons-video-alt3",
			"dashicons dashicons-vault",
			"dashicons dashicons-shield",
			"dashicons dashicons-shield-alt",
			"dashicons dashicons-sos",
			"dashicons dashicons-search",
			"dashicons dashicons-slides",
			"dashicons dashicons-analytics",
			"dashicons dashicons-chart-pie",
			"dashicons dashicons-chart-bar",
			"dashicons dashicons-chart-line",
			"dashicons dashicons-chart-area",
			"dashicons dashicons-groups",
			"dashicons dashicons-businessman",
			"dashicons dashicons-id",
			"dashicons dashicons-id-alt",
			"dashicons dashicons-products",
			"dashicons dashicons-awards",
			"dashicons dashicons-forms",
			"dashicons dashicons-testimonial",
			"dashicons dashicons-portfolio",
			"dashicons dashicons-book",
			"dashicons dashicons-book-alt",
			"dashicons dashicons-download",
			"dashicons dashicons-upload",
			"dashicons dashicons-backup",
			"dashicons dashicons-clock",
			"dashicons dashicons-lightbulb",
			"dashicons dashicons-microphone",
			"dashicons dashicons-desktop",
			"dashicons dashicons-laptop",
			"dashicons dashicons-tablet",
			"dashicons dashicons-smartphone",
			"dashicons dashicons-phone",
			"dashicons dashicons-smiley",
			"dashicons dashicons-index-card",
			"dashicons dashicons-carrot",
			"dashicons dashicons-building",
			"dashicons dashicons-store",
			"dashicons dashicons-album",
			"dashicons dashicons-palmtree",
			"dashicons dashicons-tickets-alt",
			"dashicons dashicons-money",
			"dashicons dashicons-thumbs-up",
			"dashicons dashicons-thumbs-down",
			"dashicons dashicons-layout",
			"dashicons dashicons-paperclip"
		);
		if ( "" === $id ) {
			return $icons;
		} else {
			return isset( $icons[ $id ] ) ? $icons[ $id ] : '';
		}
	}
}
/**
 *
 * @param string $version
 *
 * @return bool
 */
if ( ! function_exists( 'woocommerce_version_check' ) ) {
	function woocommerce_version_check( $version = '3.0.0' ) {
		global $woocommerce;

		if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
			return true;
		}

		return false;
	}
}