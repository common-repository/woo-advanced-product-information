<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Social_proof {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'        => "off",
			'mobile'        => "on",
			'text'          => "Views: {social_proof_views}",
			'position'      => 'after_meta',
			'fake'          => 0,
			'text_align'    => 'center',
			'border_color'  => '',
			'border_radius' => '',
			'text_color'    => '',
			'text_bg_color' => '',
			'css'           => '',
		), $data->get_params( 'social_proof' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-social_proof-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/social_proof.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$css .= '.wapinfo-social-proof{';
		$i   = 0;
		if ( $this->settings['text_align'] ) {
			$css .= 'text-align:' . $this->settings['text_align'] . ';';
		}
		if ( $this->settings['border_color'] ) {
			$i ++;
			$css .= 'border:1px solid ' . $this->settings['border_color'] . ';';
		}
		if ( $this->settings['border_radius'] ) {
			$css .= 'border-radius:' . $this->settings['border_radius'] . 'px;';
		}
		if ( $this->settings['text_color'] ) {
			$css .= 'color:' . $this->settings['text_color'] . ';';
		}
		if ( $this->settings['text_bg_color'] ) {
			$i ++;
			$css .= 'background-color:' . $this->settings['text_bg_color'] . ';';
		}
		if ( $i > 0 ) {
			$css .= 'padding: 5px 10px;';
		}
		$css .= '}';
		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-social_proof-style', $css );
		switch ( $this->settings['position'] ) {
			case 'before_meta':
				add_action( 'woocommerce_before_template_part', array( $this, 'social_proof' ) );
				break;
			default:
				add_action( 'woocommerce_after_template_part', array( $this, 'social_proof' ) );
		}
	}

	public function social_proof( $template_name ) {
		if ( 'single-product/meta.php' !== $template_name ) {
			return;
		}
		global $product;
		$product_id = $product->get_id();
		//views
		$view = get_post_meta( $product_id, '_wapi_number_of_views', true ) ? absint( get_post_meta( $product_id, '_wapi_number_of_views', true ) ) : 0;
		$old  = $view;
		$view ++;
		update_post_meta( $product_id, '_wapi_number_of_views', $view, $old );
		if ( ! $this->settings ) {
			return;
		}
		//watching
		/*
		$onltime            = 60;
		$customers_watching = array();
		$user               = array(
			'name' => isset( $_COOKIE['wapi_watching_name'] ) ? $_COOKIE['wapi_watching_name'] : "",
			'time' => isset( $_COOKIE['wapi_watching_time'] ) ? $_COOKIE['wapi_watching_time'] : ""
		);
		if ( get_post_meta( $product_id, '_customers_watching', true ) && ! empty( get_post_meta( $product_id, '_customers_watching', true ) ) ) {
			$customers_watching = get_post_meta( $product_id, '_customers_watching', true );
			foreach ( $customers_watching as $k => $v ) {
				if ( $user['name'] === $v['name'] || ( time() - $v['time'] ) > $onltime ) {
					unset( $customers_watching[ $k ] );
				}
			}

		}
		if ( ! empty( $user['name'] ) ) {
			$customers_watching[] = $user;
		}
		$onl = array();
		foreach ( $customers_watching as $cw ) {
			$onl[] = $cw;
		}
		update_post_meta( $product_id, '_customers_watching', $onl );
		*/

		$fake = 0;
		if ( $this->settings['fake'] ) {
			$fake = $this->settings['fake'];
		}
		$text = $this->settings['text'];
		$text = str_replace( '{social_proof_views}', '<span class="wapinfo-social-proof-views">' . ( $view + $fake ) . '</span>', $text );
		echo '<div class="wapinfo-social-proof">' . do_shortcode( $text ) . '</div>';
	}
}