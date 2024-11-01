<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Instock {
	protected $settings;
	protected $barwidth;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'text'             => "Only {instock_quantity} left, hurry up.",
			'fake'             => 1,
			'minrand'          => 5,
			'maxrand'          => 30,
			'position'         => 'before_cart',
			'style'            => '3',
			'text_align'       => 'left',
			'border_color'     => '',
			'border_radius'    => '',
			'text_color'       => '#ff0000',
			'text_bg_color'    => '',
			'bar_color1'       => '',
			'bar_color2'       => '',
			'width_min'        => 5,
			'width_max'        => 30,
			'css'              => '',
			'include_product'  => array(),
			'exclude_product'  => array(),
			'exclude_category' => array(),
			'include_category' => array(),
		), $data->get_params( 'instock' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'init' ) );
	}

	public function condition( $product_id ) {
		$product = wc_get_product( $product_id );
		$return  = true;
		if ( ! $product ) {
			return false;
		}
		$product_cates = $product->get_category_ids();
		if ( isset( $this->settings['include_product'] ) && is_array( $this->settings['include_product'] ) && count( $this->settings['include_product'] ) ) {
			if ( ! in_array( strval( $product_id ), $this->settings['include_product'], true ) ) {
				$return = false;
			}
		}
		if ( isset( $this->settings['exclude_product'] ) && is_array( $this->settings['exclude_product'] ) && count( $this->settings['exclude_product'] ) ) {
			if ( in_array( strval( $product_id ), $this->settings['exclude_product'], true ) ) {
				$return = false;
			}
		}

		if ( count( $product_cates ) ) {
			if ( isset( $this->settings['include_category'] ) && is_array( $this->settings['include_category'] ) && count( $this->settings['include_category'] ) ) {
				if ( ! count( array_intersect( $product_cates, $this->settings['include_category'] ) ) ) {
					$return = false;
				}
			}
			if ( isset( $this->settings['exclude_category'] ) && is_array( $this->settings['exclude_category'] ) && count( $this->settings['exclude_category'] ) ) {
				if ( count( array_intersect( $product_cates, $this->settings['exclude_category'] ) ) ) {
					$return = false;
				}
			}
		}

		return $return;
	}

	public function init() {
		global $post;
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! $this->condition( $post->ID ) ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-instock-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/instock.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$i   = 0;
		$css .= '.wapinfo-instock,.wapinfo-instock{';
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
			$css .= 'background:' . $this->settings['text_bg_color'] . ';';
		}
		$css .= '}';
		if ( $i > 0 ) {
			$css .= '.wapinfo-instock.wapinfo-instock-style-1{padding: 5px 10px;}';
		}
		if ( $this->settings['bar_color1'] ) {
			$css .= '.wapinfo-warningGradientBarLineG{background:' . $this->settings['bar_color1'] . ';}';
			$css .= '.wapinfo-instock-style-3 .wapinfo-instock-bg{background:' . $this->settings['bar_color1'] . ';}';
		}
		if ( $this->settings['bar_color2'] ) {
			$css .= '.wapinfo-instock-style-2>#wapinfo-warningGradientOuterBarG.wapinfo-instock-bg{background:' . $this->settings['bar_color2'] . ';}';
			$css .= '.wapinfo-instock-style-3 .wapinfo-instock-bg .wapinfo-instock-fill{background:' . $this->settings['bar_color2'] . ';}';
		}
		if ( get_post_meta( $post->ID, '_wapi_fake_stock', true ) ) {
			$meta  = get_post_meta( $post->ID, '_wapi_fake_stock', true );
			$today = strtotime( 'today' );
			if ( isset( $meta['day'] ) && isset( $meta['barwidth'] ) && $today == $meta['day'] ) {
				$this->barwidth = $meta['barwidth'];
			} elseif ( isset( $this->settings['width_min'] ) && isset( $this->settings['width_max'] ) ) {
				$this->barwidth = ( $this->settings['width_min'] < $this->settings['width_max'] ) ? wp_rand( absint( $this->settings['width_min'] ), absint( $this->settings['width_max'] ) ) : wp_rand( absint( $this->settings['width_min'] ), absint( $this->settings['width_min'] ) );
			}
		} elseif ( isset( $this->settings['width_min'] ) && isset( $this->settings['width_max'] ) ) {
			$this->barwidth = ( $this->settings['width_min'] < $this->settings['width_max'] ) ? wp_rand( absint( $this->settings['width_min'] ), absint( $this->settings['width_max'] ) ) : wp_rand( absint( $this->settings['width_min'] ), absint( $this->settings['width_min'] ) );
		}
		$css .= '.wapinfo-instock-style-2>#wapinfo-warningGradientOuterBarG.wapinfo-instock-bg{width:' . $this->barwidth . '%;}';
		$css .= '.wapinfo-instock-style-3 .wapinfo-instock-bg .wapinfo-instock-fill{width:' . $this->barwidth . '%;}';
		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-instock-style', $css );
		switch ( $this->settings['position'] ) {
			case 'before_cart':
				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'instock' ) );
				break;
			case 'after_cart':
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'instock' ) );
				break;
			case 'before_meta':
				add_action( 'woocommerce_product_meta_start', array( $this, 'instock' ) );
				break;
			default:
				add_action( 'woocommerce_product_meta_end', array( $this, 'instock' ) );
		}
	}


	public function instock() {
		global $product;
		$text         = $this->settings['text'];
		$product_left = 0;
		if ( $product->get_type() == 'variation' ) {
			return;
		}
		$today      = strtotime( 'today' );
		$product_id = $product->get_id();
		if ( ! empty( $this->settings['fake'] ) ) {
			if ( $product->is_in_stock() ) {
				$fake_stock = $product->get_meta( '_wapi_fake_stock' );
				if ( $fake_stock &&
				     isset( $fake_stock['day'], $fake_stock['amount'], $fake_stock['min'], $fake_stock['max'] ) &&
				     $fake_stock['day'] == $today && $fake_stock['min'] == $this->settings['minrand'] && $fake_stock['max'] != $this->settings['maxrand'] ) {
					$product_left_fake = absint( $fake_stock['amount'] );
				} else {
					$product_left_fake = wp_rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
					$product->add_meta_data(
						'_wapi_fake_stock',
						array(
							'day'      => $today,
							'amount'   => $product_left_fake,
							'barwidth' => $this->barwidth,
							'min'      => $this->settings['minrand'],
							'max'      => $this->settings['maxrand'],
						),
						true );
				}
				$product_left = $product_left_fake;
			} else {
				return;
			}
		} elseif ( $product->get_type() == 'simple' && $product->is_in_stock() ) {

			$product_left = $product->get_stock_quantity();

		}
//		if ( get_post_meta( $product_id, '_wapi_fake_stock', true ) && get_post_meta( $product_id, '_wapi_fake_stock', true )['day'] == $today ) {
//			$product_left_fake = absint( get_post_meta( $product_id, '_wapi_fake_stock', true )['amount'] );
//		} else {
//			$product_left_fake = rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
//			update_post_meta( $product_id, '_wapi_fake_stock', array(
//				'day'      => $today,
//				'amount'   => $product_left_fake,
//				'barwidth' => $this->barwidth,
//			) );
//		}
//		if ( 1 == $this->settings['fake'] ) {
//			if ( $product->get_manage_stock() ) {
//				if ( $product->is_in_stock() ) {
//					$product_left = $product->get_stock_quantity();
//				}
//			} elseif ( $product->is_in_stock() ) {
//				$product_left = $product_left_fake;
//			}
//
//
//		} elseif ( $product->get_type() == 'simple' && $product->is_in_stock() ) {
//
//			$product_left = $product->get_stock_quantity();
//
//		}
		if ( $product_left < 1 ) {
			return;
		}
		$text = str_replace( '{instock_quantity}', $product_left, $text );
		echo '<div class="wapinfo-instock wapinfo-instock-style-' . esc_attr( isset( $this->settings['style'] ) ? $this->settings['style'] : '1' ) . '">';
		if ( isset( $this->settings['style'] ) ) {
			if ( 2 == $this->settings['style'] ) {
				echo '<div id="wapinfo-warningGradientOuterBarG" class="wapinfo-instock-bg">
	<div id="wapinfo-warningGradientFrontBarG" class="wapinfo-warningGradientAnimationG">
		<div class="wapinfo-warningGradientBarLineG"></div>
		<div class="wapinfo-warningGradientBarLineG"></div>
		<div class="wapinfo-warningGradientBarLineG"></div>
		<div class="wapinfo-warningGradientBarLineG"></div>
		<div class="wapinfo-warningGradientBarLineG"></div>
		<div class="wapinfo-warningGradientBarLineG"></div>
	</div>
</div>';
			} elseif ( 3 == $this->settings['style'] ) {
				echo '<div class="wapinfo-instock-bg">
	<div class="wapinfo-instock-fill">
	</div>
</div>';
			}
		}
		echo '<div class="wapinfo-instock-show">' . do_shortcode( $text ) . '</div></div>';

	}


}
