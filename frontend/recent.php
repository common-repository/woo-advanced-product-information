<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Recent {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'range'            => 30,
			'text'             => '{recent_quantity} orders in the last {recent_range} days.',
			'position'         => 'after_meta',
			'fake'             => 1,
			'minrand'          => 10,
			'maxrand'          => 50,
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
		), $data->get_params( 'recent' ) );
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
		if ( ! $this->condition( $post->ID ) ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-recent-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/recent.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$i   = 0;
		$css .= '.wapinfo-recent-order{';
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
		wp_add_inline_style( 'wapinfo-frontend-recent-style', $css );

		switch ( $this->settings['position'] ) {
			case 'before_cart':
				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'recent' ) );
				break;
			case 'after_cart':
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'recent' ) );
				break;
			case 'before_meta':
				add_action( 'woocommerce_before_template_part', array( $this, 'recent_meta' ) );
				break;
			default:
				add_action( 'woocommerce_after_template_part', array( $this, 'recent_meta' ) );
		}
	}

	public function recent() {
		global $product;
		$product_id = $product->get_id();
		$range      = $this->settings['range'];
		$qty        = 0;
		$today      = strtotime( 'today' );

		if ( 1 === $this->settings['fake'] ) {
			$fake = $product->get_meta( '_wapi_recent_quantity_virtual' );
			if ( $fake &&
			     isset( $fake['day'], $fake['amount'], $fake['min'], $fake['max'] ) &&
			     $fake['day'] == $today && $fake['min'] == $this->settings['minrand'] && $fake['max'] != $this->settings['maxrand'] ) {
				$qty = absint( $fake['amount'] );
			} else {
				$qty = wp_rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
				$product->add_meta_data(
					'_wapi_recent_quantity_virtual',
					array(
						'day'    => $today,
						'amount' => $qty,
						'min'    => $this->settings['minrand'],
						'max'    => $this->settings['maxrand'],
					),
					true );
			}
//			if ( get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true ) && get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true )['day'] == $today ) {
//				$qty = absint( get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true )['amount'] );
//			} else {
//				$qty = rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
//				update_post_meta( $product_id, '_wapi_recent_quantity_virtual', array(
//					'day'    => $today,
//					'amount' => $qty
//				) );
//			}
		} else {
			$from            = $today - $range * 24 * 3600;
			$recent_quantity = get_post_meta( $product_id, '_wapi_recent_quantity', true );

			if ( ! $recent_quantity || $recent_quantity['day'] != $today ) {
				$qty = self::sold_quantity( $product_id, $from, $today );
				update_post_meta( $product_id, '_wapi_recent_quantity', array(
					'day'    => $today,
					'amount' => $qty
				) );
			} else {
				$qty = $recent_quantity['amount'];
			}
		}
		if ( $qty > 0 ) {
			$text = $this->settings['text'];
			$text = str_replace( '{recent_quantity}', '<span class="wapinfo-recent-qty">' . $qty . '</span>', $text );
			$text = str_replace( '{recent_range}', '<span class="wapinfo-recent-range">' . $range . '</span>', $text );
			echo '<div class="wapinfo-recent-order">' . do_shortcode( $text ) . '</div>';
		}
	}

	public function recent_meta( $template_name ) {
		if ( 'single-product/meta.php' !== $template_name ) {
			return;
		}
		global $product;
		$product_id = $product->get_id();
		$range      = $this->settings['range'];
		$qty        = 0;
		$today      = strtotime( 'today' );

		if ( 1 === $this->settings['fake'] ) {
			$fake = $product->get_meta( '_wapi_recent_quantity_virtual' );
			if ( $fake &&
			     isset( $fake['day'], $fake['amount'], $fake['min'], $fake['max'] ) &&
			     $fake['day'] == $today && $fake['min'] == $this->settings['minrand'] && $fake['max'] != $this->settings['maxrand'] ) {
				$qty = absint( $fake['amount'] );
			} else {
				$qty = wp_rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
				$product->add_meta_data(
					'_wapi_recent_quantity_virtual',
					array(
						'day'    => $today,
						'amount' => $qty,
						'min'    => $this->settings['minrand'],
						'max'    => $this->settings['maxrand'],
					),
					true );
			}
//			if ( get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true ) && get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true )['day'] == $today ) {
//				$qty = absint( get_post_meta( $product_id, '_wapi_recent_quantity_virtual', true )['amount'] );
//			} else {
//				$qty = rand( absint( $this->settings['minrand'] ), absint( $this->settings['maxrand'] ) );
//				update_post_meta( $product_id, '_wapi_recent_quantity_virtual', array(
//					'day'    => $today,
//					'amount' => $qty
//				) );
//			}
		} else {
			$from            = $today - $range * 24 * 3600;
			$recent_quantity = get_post_meta( $product_id, '_wapi_recent_quantity', true );
			if ( ! $recent_quantity || $recent_quantity['day'] != $today ) {
				$qty = self::sold_quantity( $product_id, $from, $today );
				update_post_meta( $product_id, '_wapi_recent_quantity', array(
					'day'    => $today,
					'amount' => $qty
				) );
			} else {
				$qty = $recent_quantity['amount'];
			}
		}
		if ( $qty > 0 ) {
			$text = $this->settings['text'];
			$text = str_replace( '{recent_quantity}', '<span class="wapinfo-recent-qty">' . $qty . '</span>', $text );
			$text = str_replace( '{recent_range}', '<span class="wapinfo-recent-range">' . $range . '</span>', $text );
			echo '<div class="wapinfo-recent-order">' . do_shortcode( $text ) . '</div>';
		}
	}

	public static function sold_quantity( $product_id, $from, $to ) {
		$qty  = 0;
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-on-hold', 'wc-completed', 'wc-processing' ),
			'posts_per_page' => - 1,
			'date_query'     => array(
				array(
					'after'     => gmdate( 'Y-m-d', $from ),
					'before'    => gmdate( 'Y-m-d', $to ),
					'inclusive' => true,
				)
			)
		);

		$orders = wc_get_orders( $args );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				foreach ( $order->get_items() as $item_data ) {
					if ( $product_id == $item_data->get_product_id() ) {
						$qty += $item_data->get_quantity();
					}
				};
			}
		}

		return $qty;
	}
}