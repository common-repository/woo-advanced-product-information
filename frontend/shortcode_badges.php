<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Shortcode_badges {
	protected $settings;

	function __construct() {
		add_action( 'init', array( $this, 'shortcode_init' ) );
	}

	public function shortcode_init() {
		add_shortcode( 'wapinfo_badges', array( $this, 'register_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'shortcode_enqueue_script' ) );
	}

	public function shortcode_enqueue_script() {
		if ( ! wp_script_is( 'wapinfo-shortcode-badges-style', 'registered' ) ) {
			wp_register_style( 'wapinfo-shortcode-badges-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'shortcode-badges-style.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		}
	}

	public function register_shortcode( $atts, $content = "" ) {
		global $wapinfo_shortcode_badge_id;
		$wapinfo_shortcode_badge_id ++;
		extract( shortcode_atts( array(
			'id'       => 1,
			'color'    => '',
			'bg_color' => '',
		), $atts ) );
		if ( ! wp_script_is( 'wapinfo-shortcode-badges-style', 'enqueued' ) ) {
			wp_enqueue_style( 'wapinfo-shortcode-badges-style' );
			$css = '';
			if ( $color ) {
				$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges{color:' . $color . ' !important;}';
			}
			if ( $bg_color ) {
				$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges{background:' . $bg_color . ';}';
				switch ( $id ) {
					case 1:
					case 2:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-top-color:' . $bg_color . ';border-bottom-color:' . $bg_color . ';}';
						break;
					case 3:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-top-color:' . $bg_color . ';border-bottom-color:' . $bg_color . ';}';
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-left-color:' . $bg_color . ';}';
						break;
					case 4:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-top-color:' . $bg_color . ';border-bottom-color:' . $bg_color . ';}';
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-right-color:' . $bg_color . ';}';
						break;
					case 5:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before,#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-top-color:' . $bg_color . ';border-bottom-color:' . $bg_color . ';}';
						break;
					case 6:
					case 7:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-bottom-color:' . $bg_color . ';}';
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-top-color:' . $bg_color . ';}';
						break;
					case 8:
					case 9:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-right-color:' . $bg_color . ';}';
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-left-color:' . $bg_color . ';}';
						break;
					case 13:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':after{border-left-color:' . $bg_color . ';}';
						break;
					case 14:
						$css .= '#wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '.wapinfo-shortcode-badges-' . $id . ':before{border-right-color:' . $bg_color . ';}';
						break;
				}
			}
			wp_add_inline_style( 'wapinfo-shortcode-badges-style', $css );
		}

		return do_shortcode( '<span id="wapinfo-shortcode-badges-' . $wapinfo_shortcode_badge_id . '" class="wapinfo-shortcode-badges wapinfo-shortcode-badges-' . $id . '"><span class="wapinfo-shortcode-badges-content">' . $content . '</span></span>' );
	}
}