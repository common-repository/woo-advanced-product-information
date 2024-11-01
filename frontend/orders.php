<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Orders {
	protected $settings;
	protected $total;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'quantity'         => 3,
			'text'             => '{customers_list} recently bought this product.',
			'position'         => 'after_cart',
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
		), $data->get_params( 'orders' ) );
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

		wp_enqueue_style( 'wapinfo-frontend-orders-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/orders.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$i   = 0;
		$css .= '.wapinfo-orders-text{';
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
		wp_add_inline_style( 'wapinfo-frontend-orders-style', $css );
		switch ( $this->settings['position'] ) {
			case 'before_cart':
				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'orders' ) );
				break;
			case 'after_cart':
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'orders' ) );
				break;
			case 'before_meta':
				add_action( 'woocommerce_before_template_part', array( $this, 'orders_meta' ) );
				break;
			default:
				add_action( 'woocommerce_after_template_part', array( $this, 'orders_meta' ) );
		}
	}

	public function orders() {
		global $product;
		global $wpdb;
		$limit        = isset( $this->settings['quantity'] ) ? $this->settings['quantity'] : 3;
		$product_id   = $product->get_id();
		$results = wp_cache_get( 'wapi_order_cache' );
		$results = $results ?: [];

		if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			if ( empty( $result ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_items.order_id
			        FROM {$wpdb->prefix}woocommerce_order_items as order_items
			        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			        LEFT JOIN {$wpdb->prefix}wc_orders AS posts ON order_items.order_id = posts.id
			        WHERE posts.type = 'shop_order'
			        AND posts.status IN ( 'wc-processing', 'wc-completed' )
			        AND order_items.order_item_type = 'line_item'
			        AND order_item_meta.meta_key = '_product_id'
			        AND order_item_meta.meta_value = %s
			        ORDER BY posts.id DESC
			        LIMIT %d",
					$product_id,
					$limit
				) );
				wp_cache_set( 'wapi_order_cache', $results );
			}
		} else {
			if ( empty( $result ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_items.order_id
			        FROM {$wpdb->prefix}woocommerce_order_items as order_items
			        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			        WHERE posts.post_type = 'shop_order'
			        AND posts.post_status IN ( 'wc-processing', 'wc-completed' )
			        AND order_items.order_item_type = 'line_item'
			        AND order_item_meta.meta_key = '_product_id'
			        AND order_item_meta.meta_value = %s
			        ORDER BY posts.ID DESC
			        LIMIT %d",
					$product_id,
					$limit
				) );
				wp_cache_set( 'wapi_order_cache', $results );
			}
		}

		if ( count( $results ) ) {
			$customers = array();
			$countries = array();
			foreach ( $results as $result ) {
				$order       = wc_get_order( $result );
				$name        = ucwords( $order->get_billing_first_name() );
				$customers[] = $name;
				$countries[] = $name . ' from ' . WC()->countries->countries[ $order->get_billing_country() ];
			}
			if ( count( $customers ) > 1 ) {
				$last      = ' and ' . array_pop( $customers );
				$customers = implode( ', ', $customers );
				$customers .= $last;
				$last      = ' and ' . array_pop( $countries );
				$countries = implode( ', ', $countries );
				$countries .= $last;
			} else {
				$customers = implode( ', ', $customers );
				$countries = implode( ', ', $countries );
			}

			$text = $this->settings['text'];
			$text = str_replace( '{customers_list}', '<span class="wapinfo-orders-list">' . $customers . '</span>', $text );
			$text = str_replace( '{customers_list_with_countries}', '<span class="wapinfo-orders-list">' . $countries . '</span>', $text );
			echo '<div class="wapinfo-orders-text">' . do_shortcode( $text ) . '</div>';
		}
	}

	public function orders_meta( $template_name ) {
		if ( 'single-product/meta.php' !== $template_name ) {
			return;
		}
		global $product;
		global $wpdb;
		$limit        = isset( $this->settings['quantity'] ) ? $this->settings['quantity'] : 3;
		$product_id   = $product->get_id();
		$results = wp_cache_get( 'wapi_order_cache' );
		$results = $results ?: [];

		if ( class_exists( '\Automattic\WooCommerce\Utilities\OrderUtil' ) && \Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled() ) {
			if ( empty( $result ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_items.order_id
			        FROM {$wpdb->prefix}woocommerce_order_items as order_items
			        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			        LEFT JOIN {$wpdb->prefix}wc_orders AS posts ON order_items.order_id = posts.id
			        WHERE posts.type = 'shop_order'
			        AND posts.status IN ( 'wc-processing', 'wc-completed' )
			        AND order_items.order_item_type = 'line_item'
			        AND order_item_meta.meta_key = '_product_id'
			        AND order_item_meta.meta_value = %s
			        ORDER BY posts.id DESC
			        LIMIT %d",
					$product_id,
					$limit
				) );
				wp_cache_set( 'wapi_order_cache', $results );
			}
		} else {
			if ( empty( $result ) ) {
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
				$results = $wpdb->get_col( $wpdb->prepare(
					"SELECT order_items.order_id
			        FROM {$wpdb->prefix}woocommerce_order_items as order_items
			        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
			        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
			        WHERE posts.post_type = 'shop_order'
			        AND posts.post_status IN ( 'wc-processing', 'wc-completed' )
			        AND order_items.order_item_type = 'line_item'
			        AND order_item_meta.meta_key = '_product_id'
			        AND order_item_meta.meta_value = %s
			        ORDER BY posts.ID DESC
			        LIMIT %d",
					$product_id,
					$limit
				) );
				wp_cache_set( 'wapi_order_cache', $results );
			}
		}

		if ( count( $results ) ) {
			$customers = array();
			$countries = array();
			foreach ( $results as $result ) {
				$order       = wc_get_order( $result );
				$name        = ucwords( $order->get_billing_first_name() );
				$customers[] = $name;
				$countries[] = $name . ' from ' . WC()->countries->countries[ $order->get_billing_country() ];
			}
			if ( count( $customers ) > 1 ) {
				$last      = ' and ' . array_pop( $customers );
				$customers = implode( ', ', $customers );
				$customers .= $last;
				$last      = ' and ' . array_pop( $countries );
				$countries = implode( ', ', $countries );
				$countries .= $last;
			} else {
				$customers = implode( ', ', $customers );
				$countries = implode( ', ', $countries );
			}
			$text = $this->settings['text'];
			$text = str_replace( '{customers_list}', '<span class="wapinfo-orders-list">' . $customers . '</span>', $text );
			$text = str_replace( '{customers_list_with_countries}', '<span class="wapinfo-orders-list">' . $countries . '</span>', $text );
			echo '<div class="wapinfo-orders-text">' . do_shortcode( $text ) . '</div>';
		}
	}
}