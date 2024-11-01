<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Review {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'no_review'        => '[wapi_icon id="161"]Be the first to review this product.',
			'satisfied'        => '[wapi_icon id="367"]{satisfied_rate} of buyers are satisfied with this product.',
			'min_rate'         => 80,
			'position'         => 'before_rating',
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
		), $data->get_params( 'review' ) );
		if ( empty( get_option( 'woocommerce_enable_reviews' ) ) || 'no' === get_option( 'woocommerce_enable_reviews' ) ) {
			return;
		}
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
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

	public function frontend_enqueue() {
		global $post;
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
			return;
		}
		if ( ! $this->condition( $post->ID ) ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-review-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/review.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$i   = 0;
		$css .= '.wapinfo-review{';
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
		if ( $this->settings['text_bg_color'] ) {
			$i ++;
			$css .= 'background-color:' . $this->settings['text_bg_color'] . ';';
		}
		if ( $i > 0 ) {
			$css .= 'padding: 5px 10px;';
		}
		$css .= '}';
		if ( $this->settings['text_color'] ) {
			$css .= '.wapinfo-review>.wapinfo-review-a{color:' . $this->settings['text_color'] . ';}';
		}
		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-review-style', $css );
		add_action( 'woocommerce_before_template_part', array( $this, 'review' ) );
	}

	public function review( $template_name ) {
		if ( 'single-product/rating.php' !== $template_name ) {
			return;
		}
		global $product;

		$review_num = $product->get_review_count( 'view' );
		if ( 0 == $review_num ) {
			echo '<div class="wapinfo-review">';
			echo '<a href="#reviews" class="wapinfo-review-a woocommerce-review-link" rel="nofollow" >' . do_shortcode( $this->settings['no_review'] ) . '</a></div>';
		} else {
			$satified      = $product->get_review_count();
			$satified_rate = round( 100 * $satified / $review_num );
			if ( $satified_rate >= $this->settings['min_rate'] ) {
				$text = str_replace( '{satisfied_rate}', $satified_rate . '%', $this->settings['satisfied'] );
				echo '<div class="wapinfo-review">';
				echo '<a href="#reviews" class="wapinfo-review-a woocommerce-review-link" rel="nofollow">' . do_shortcode( $text ) . '</a></div>';
			}
		}
	}
}