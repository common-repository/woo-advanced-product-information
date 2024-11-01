<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Shortcode_icons {
	protected $settings;

	function __construct() {
		add_action( 'init', array( $this, 'shortcode_init' ) );
	}

	public function shortcode_init() {
		add_shortcode( 'wapi_icon', array( $this, 'register_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'shortcode_enqueue_script' ) );
	}

	public function shortcode_enqueue_script() {
		if ( ! wp_script_is( 'woo-advanced-product-information-icons-shortcode-css', 'registered' ) ) {
			wp_register_style( 'woo-advanced-product-information-icons-shortcode-css', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'shortcode-icons.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		}
	}

	public function register_shortcode( $atts, $content = "" ) {
		global $wapinfo_shortcode_icon_id;
		$wapinfo_shortcode_icon_id ++;
		$arr = shortcode_atts( array(
			'id'    => '',
			'color' => '',
			'size'  => '',
		), $atts );
		if ( ! wp_script_is( 'woo-advanced-product-information-icons-shortcode-css' ) ) {
			wp_enqueue_style( 'woo-advanced-product-information-icons-shortcode-css', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'shortcode.css', [], VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			$css = '#wapi-shortcode-icon-' . $wapinfo_shortcode_icon_id . '.wapi-shortcode-icon{vertical-align:middle;';
			if ( $arr['color'] ) {
				$css .= 'color:' . $arr['color'] . ' !important;';
			}
			if ( $arr['size'] ) {
				$css .= 'font-size:' . $arr['size'] . 'px;';
				$css .= 'height:' . $arr['size'] . 'px;';
				$css .= 'line-height:' . $arr['size'] . 'px;';
			}
			$css .= '}';
			wp_add_inline_style( 'woo-advanced-product-information-icons-shortcode-css', $css );
		}
		ob_start();
		?>
        <span id="wapi-shortcode-icon-<?php echo esc_attr($wapinfo_shortcode_icon_id); ?>"
              class="<?php echo esc_attr( wapi_get_icon( $arr['id'] ) ); ?> wapi-shortcode-icon"></span>
		<?php
		$return = ob_get_clean();

		return $return;
	}
}