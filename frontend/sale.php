<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Sale {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'text'             => '[wapinfo_badges id="11"]-{sale_percent}[/wapinfo_badges]',
			'position'         => 'saleflash',
			'text_align'       => 'left',
			'border_color'     => '',
			'border_radius'    => '',
			'text_color'       => '',
			'text_bg_color'    => '',
			'css'              => '',
			'include_product'  => array(),
			'exclude_product'  => array(),
			'exclude_category' => array(),
			'include_category' => array(),
		), $data->get_params( 'sale' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		if ( is_post_type_archive( 'product' ) || is_tax( 'product_cat' ) || ( is_product() && is_single() ) ) {
			global $post;
//			$local = get_post_meta( $post->ID, '_wapi_settings', true );
//			if ( isset( $local['sale']['local'] ) && 'on' == $local['sale']['local'] ) {
//				$this->settings = $local['sale'];
//			}
			if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
				return;
			}
			if ( ! $this->condition( $post->ID ) ) {
				return;
			}
			wp_enqueue_style( 'wapinfo-frontend-sale-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/sale.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			$css = '';
			$i   = 0;
			$css .= '.wapinfo-sale{';
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
			wp_add_inline_style( 'wapinfo-frontend-sale-style', $css );
			if ( isset( $this->settings['position'] ) && 'after_price' == $this->settings['position'] ) {
				add_action( 'woocommerce_after_template_part', array( $this, 'sale' ), 10 );
			} else {
				add_filter( 'woocommerce_sale_flash', array( $this, 'woocommerce_sale_flash' ), 10, 3 );
			}
		}
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

	public function sale( $template_name ) {
		global $product;
		if ( 'single-product/price.php' != $template_name ) {
			return;
		}
		$text = $this->settings['text'];
		if ( in_array( $product->get_type(), array( 'simple', 'external' ), true ) && $product->is_on_sale() ) {
			$regular_price = $product->get_regular_price();
			$sale_price    = $product->get_sale_price();
			if ( wc_tax_enabled() && 'no' === get_option( 'woocommerce_prices_include_tax' ) && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$regular_price = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );
				$sale_price    = wc_get_price_including_tax( $product, array( 'price' => $sale_price ) );
			}
			$sale_amount  = ( $regular_price ) - ( $sale_price );
			$sale_percent = 100 * $sale_amount / ( $regular_price );
			if ( $sale_amount > 0 ) {
				$text = str_replace( '{sale_amount}', '<span class="wapinfo-sale-amount">' . wc_price( $sale_amount ) . '</span>', $text );
				$text = str_replace( '{sale_percent}', '<span class="wapinfo-sale-percent">' . round( $sale_percent, 0 ) . "%</span>", $text );
				echo '<div class="wapinfo-sale">' . do_shortcode( $text ) . '</div>';
			}
		} elseif ( $product->get_type() == 'variation' ) {

		}
	}

	public function woocommerce_sale_flash( $html, $post, $product ) {
		$text = $this->settings['text'];
		if ( in_array( $product->get_type(), array( 'simple', 'external' ), true ) && $product->is_on_sale() ) {
			$regular_price = $product->get_regular_price();
			$sale_price    = $product->get_sale_price();
			if ( wc_tax_enabled() && 'no' === get_option( 'woocommerce_prices_include_tax' ) && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
				$regular_price = wc_get_price_including_tax( $product, array( 'price' => $regular_price ) );
				$sale_price    = wc_get_price_including_tax( $product, array( 'price' => $sale_price ) );
			}
			$sale_amount  = ( $regular_price ) - ( $sale_price );
			$sale_percent = 100 * $sale_amount / ( $regular_price );
			if ( $sale_amount > 0 ) {
				$text = str_replace( '{sale_amount}', '<span class="wapinfo-sale-amount">' . wc_price( $sale_amount ) . '</span>', $text );
				$text = str_replace( '{sale_percent}', '<span class="wapinfo-sale-percent">' . round( $sale_percent, 0 ) . "%</span>", $text );
				$html = '<div class="wapinfo-sale">' . do_shortcode( $text ) . '</div>';
			}
		} elseif ( $product->get_type() == 'variation' ) {

		}

		return $html;
	}
}