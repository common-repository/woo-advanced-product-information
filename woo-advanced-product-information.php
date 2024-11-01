<?php
/**
 * Plugin Name: Advanced Product Information for WooCommerce
 * Plugin URI: https://villatheme.com/extensions/
 * Description: Makes your product page informative with additional info, such as: Review, Stock, Sales, Countdown, Coupon, Social Proof, Rank and more.
 * Version: 1.1.4
 * Author: VillaTheme
 * Author URI: http://villatheme.com
 * Copyright 2018-2024 VillaTheme.com. All rights reserved.
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: woo-advanced-product-information
 * Domain Path: /languages
 * Requires Plugins: woocommerce
 * Requires at least: 5.0
 * Tested up to: 6.5
 * WC tested up to: 8.8
 * WC requires at least: 7.0
 * Requires PHP: 7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION', '1.1.4' );

/**
 * Class WC_ADVANCED_PRODUCT_INFORMATION
 */
class WC_ADVANCED_PRODUCT_INFORMATION {
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'install' ) );
		add_action( 'before_woocommerce_init', [ $this, 'custom_order_tables_declare_compatibility' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ], 20 );
	}

	public function init() {
		if ( ! class_exists( 'VillaTheme_Require_Environment' ) ) {
			include_once plugin_dir_path( __FILE__ ) . 'includes/support.php';
		}

		$environment = new \VillaTheme_Require_Environment( [
				'plugin_name' => 'Advanced Product Information for WooCommerce',
				'php_version' => '7.0',
				'wp_version'  => '5.0',
				'wc_version'  => '7.0',
				'require_plugins' => [
					[
						'slug' => 'woocommerce',
						'name' => 'WooCommerce',
					],
				],
			]
		);

		if ( $environment->has_error() ) {
			return;
		}

		require_once plugin_dir_path( __FILE__ ) . 'includes/define.php';
	}

	/**
	 * When active plugin Function will be call
	 */
	public static function install() {
		if ( ! get_option( '_wapi_settings', '' ) ) {
			$wapi_settings_args = array(
				'review'       => array(
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
				),
				'instock'      => array(
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
				),
				'shipping'     => array(
					'enable' => 'on',
					'mobile' => "on",
					'css'    => '',
				),
				'sale'         => array(
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
				),
				'countdown'    => array(
					'enable'           => "on",
					'mobile'           => "on",
					'loop'             => 1,
					'fake'             => 1,
					'start'            => strtotime( gmdate( "Y-m-d H:i", time() - 3600 ) ),
					'end'              => strtotime( gmdate( "Y-m-d H:i", time() + 5 * 24 * 3600 ) ),
					'text'             => '{countdown_timer}',
					'style'            => 3,
					'type'             => 1,
					'position'         => 'after_price',
					'text_align'       => 'center',
					'border_color'     => '',
					'border_radius'    => '',
					'text_color'       => '#ffffff',
					'text_bg_color'    => '#70abb2',
					'css'              => '',
					'include_product'  => array(),
					'exclude_product'  => array(),
					'exclude_category' => array(),
					'include_category' => array(),
				),
				'recent'       => array(
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
				),
				'rank'         => array(
					'enable'           => "on",
					'mobile'           => "on",
					'catnum'           => 2,
					'min'              => 5,
					'by'               => 'month',
					'text'             => '[wapinfo_badges id="3"]#{rank} best sellers[/wapinfo_badges] in {category}{time}.',
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
				),
				'payment'      => array(
					'enable'        => 'on',
					'mobile'        => "on",
					'position'      => "after_meta",
					'border_radius' => '',
					'id'            => array(),
					'icon'          => array(),
					'url'           => array(),
					'active'        => array(),
					'css'           => '',
				),
				'social_proof' => array(
					'enable'        => "on",
					'mobile'        => "on",
					'text'          => '[wapi_icon id="91" color="" size="22"] {social_proof_views}',
					'position'      => 'after_meta',
					'fake'          => 1567,
					'text_align'    => 'left',
					'border_color'  => '',
					'border_radius' => '',
					'text_color'    => '',
					'text_bg_color' => '',
					'css'           => '',
				),
				'coupon'       => array(
					'enable'        => "on",
					'mobile'        => "on",
					'text'          => 'Enter code:{coupon_code} to save {coupon_amount}.',
					'code'          => '',
					'position'      => 'before_cart',
					'border_radius' => '',
					'border_color'  => '',
					'text_color'    => '',
					'text_bg_color' => '',
					'css'           => '',
				),
				'custom'       => array(
					'enable'     => "on",
					'mobile'     => "on",
					'layout'     => "2",
					'position'   => "after_meta",
					'font_size'  => '14',
					'icon_width' => '35',
					'icon_color' => '#5a7ea8',
					'text_color' => '',
					'background' => '#efefef',
					'css'        => '',
					'row'        => array(
						'icon'    => array( 109, 65, 50 ),
						'heading' => array( 'Safe Payment', 'Support 24/7', 'Fast Shipping' ),
						'text'    => array(
							'Pay with the world’s most popular and secure payment methods',
							'Round-the-clock assistance for a smooth shopping experience.',
							'Shipping within 24 hours after order completed.'
						),
						'url'     => array( '', '', '' ),
					)
				),
				'orders'       => array(
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
				),
			);
			update_option( '_wapi_settings', $wapi_settings_args );
		}
	}

	public function custom_order_tables_declare_compatibility() {
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
}

new WC_ADVANCED_PRODUCT_INFORMATION();