<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Admin_Admin {
	protected $settings;

	function __construct() {
		add_filter( 'plugin_action_links_woo-advanced-product-information/woo-advanced-product-information.php', array(
			$this,
			'settings_link'
		) );

		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
	}


	/**
	 * Init Script in Admin
	 */
	public function admin_enqueue_scripts() {
		$screen_id = get_current_screen()->id;
		if ( 'toplevel_page_woo-advanced-product-information' === $screen_id ) {
			global $wp_scripts;
			$scripts = $wp_scripts->registered;
			foreach ( $scripts as $k => $script ) {
				preg_match( '/select2/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
				preg_match( '/bootstrap/i', $k, $result );
				if ( count( array_filter( $result ) ) ) {
					unset( $wp_scripts->registered[ $k ] );
					wp_dequeue_script( $script->handle );
				}
			}

			wp_enqueue_script( 'wapi_admin_select2_script', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'select2.min.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_script( 'wapi_admin_address_script', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'address-1.6.min.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapi_admin_seletct2', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'select2.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_script( 'wapi-semantic-js-form', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'form.min.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapi-semantic-css-form', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'form.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_script( 'wapi-semantic-js-checkbox', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'checkbox.min.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapi-semantic-css-checkbox', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'checkbox.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-button', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'button.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_script( 'wapi-semantic-js-tab', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'tab.min.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapi-semantic-css-tab', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'tab.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-menu', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'menu.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-input', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'input.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-table', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'table.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-segment', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'segment.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-label', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'label.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-semantic-css-icon', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'icon.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_style( 'wapi-admin-icons', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'woo-advanced-product-information-icons.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			wp_enqueue_script( 'jquery-ui-sortable' );
			/*Color picker*/
			wp_enqueue_script( 'iris', admin_url( 'js/iris.min.js' ), array(
				'jquery-ui-draggable',
				'jquery-ui-slider',
				'jquery-touch-punch'
			), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_script( 'wapi-admin-javascript', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'admin-javascript.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapi-admin-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'admin-style.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			if ( is_rtl() ) {
				if ( WP_DEBUG ) {
					wp_enqueue_style( 'wapi-admin-style-rtl', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'admin-style-rtl.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
				} else {
					wp_enqueue_style( 'wapi-admin-style-rtl', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'admin-style-rtl.min.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
				}
			}
		}
	}

	/**
	 * Link to Settings
	 *
	 * @param $links
	 *
	 * @return mixed
	 */
	public function settings_link( $links ) {
		$settings_link = '<a href="admin.php?page=woo-advanced-product-information" title="' . esc_html__( 'Settings', 'woo-advanced-product-information' ) . '">' . esc_html__( 'Settings', 'woo-advanced-product-information' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}


	/**
	 * Function init when run plugin+
	 */
	function init() {
		/*Register post type*/
		load_plugin_textdomain( 'woo-advanced-product-information' );
		$this->load_plugin_textdomain();
		if ( class_exists( 'VillaTheme_Support' ) ) {
			new VillaTheme_Support( array(
				'support'    => 'https://wordpress.org/support/plugin/woo-advanced-product-information/',
				'docs'       => 'http://docs.villatheme.com/?item=woo-advanced-product-information',
				'review'     => 'https://wordpress.org/support/plugin/woo-advanced-product-information/reviews/?rate=5#rate-response',
				'pro_url'    => '',
				'css'        => VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS,
				'image'      => VI_WC_ADVANCED_PRODUCT_INFORMATION_IMAGES,
				'slug'       => 'woo-advanced-product-information',
				'menu_slug'  => 'woo-advanced-product-information',
				'survey_url' => 'https://script.google.com/macros/s/AKfycbxnkGbcciQY8qH8v47ncm-CtjP7WMsmJS4TJ9yYo_ZBWM3YijCnnmDzNsu27HjlDkpH8g/exec',
				'version'    => VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION
			) );
		}
	}


	/**
	 * load Language translate
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'woo-advanced-product-information' );
		// Global + Frontend Locale
		load_textdomain( 'woo-advanced-product-information', VI_WC_ADVANCED_PRODUCT_INFORMATION_LANGUAGES . "woo-advanced-product-information-$locale.mo" );
		load_plugin_textdomain( 'woo-advanced-product-information', false, VI_WC_ADVANCED_PRODUCT_INFORMATION_LANGUAGES );
	}

	/**
	 * Register a custom menu page.
	 */
	public function menu_page() {
		add_menu_page(
			esc_html__( 'Advanced Product Information for WooCommerce', 'woo-advanced-product-information' ),
			esc_html__( 'WC Product Information', 'woo-advanced-product-information' ),
			'manage_options',
			'woo-advanced-product-information',
			array(
				'WC_ADVANCED_PRODUCT_INFORMATION_Admin_Settings',
				'settings_page'
			), 'dashicons-info', 2 );
	}
}
