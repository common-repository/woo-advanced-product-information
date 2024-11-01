<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_frontend {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
	}

	public function enqueue_script() {
		wp_enqueue_style( 'woo-advanced-product-information-icons', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'woo-advanced-product-information-icons.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		if ( WP_DEBUG ) {
			wp_enqueue_style( 'woo-advanced-product-information-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'woo-advanced-product-information-style.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );

		} else {
			wp_enqueue_style( 'woo-advanced-product-information-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'woo-advanced-product-information-style.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		}
		if ( is_rtl() ) {
			if ( WP_DEBUG ) {
				wp_enqueue_style( 'woo-advanced-product-information-rtl', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'rtl.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			} else {
				wp_enqueue_style( 'woo-advanced-product-information-rtl', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'rtl.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			}
		}
	}
}

new WC_ADVANCED_PRODUCT_INFORMATION_frontend();