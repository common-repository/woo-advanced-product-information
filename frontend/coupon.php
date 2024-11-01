<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Coupon {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'        => "off",
			'mobile'        => "on",
			'text'          => 'Enter code:{coupon_code} to save {coupon_amount}.',
			'code'          => '',
			'position'      => 'before_cart',
			'border_radius' => '',
			'border_color'  => '',
			'text_color'    => '',
			'text_bg_color' => '',
			'css'           => '',
		), $data->get_params( 'coupon' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );

	}

	public function frontend_enqueue() {
		if ( ! $this->settings || 'on' != $this->settings['enable'] || ! $this->settings['code'] ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-coupon-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/coupon.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		$css .= '.wapinfo-coupon{';
		if ( '' != $this->settings['text_color'] ) {
			$css .= 'color:' . $this->settings['text_color'] . ';';
		}
		if ( '' != $this->settings['text_bg_color'] ) {
			$css .= 'background-color:' . $this->settings['text_bg_color'] . ';';
		}
		if ( $this->settings['border_radius'] ) {
			$css .= 'border-radius:' . $this->settings['border_radius'] . 'px;';
		}
		if ( '' != $this->settings['border_color'] ) {
			$css .= 'border-color:' . $this->settings['border_color'] . ';';
		}
		$css .= '}';

		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-coupon-style', $css );
		switch ( $this->settings['position'] ) {

			case 'before_price':
				add_action( 'woocommerce_before_template_part', array( $this, 'coupon' ), 9 );
				break;
			case 'after_price':
				add_action( 'woocommerce_after_template_part', array( $this, 'coupon' ), 12 );
				break;
			case 'before_cart':
				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'coupon_cart' ) );
				break;
			case 'after_cart':
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'coupon_cart' ) );
				break;

		}
	}

	public function coupon( $template_name ) {
		global $product;

		if ( in_array( $this->settings['position'], array(
				'before_price',
				'after_price'
			), true ) && 'single-product/price.php' != $template_name ) {
			return;
		}
		$coupon = new WC_Coupon( $this->settings['code'] );
		if ( ! $coupon || ! $coupon->is_valid_for_product( $product ) && ! $coupon->is_valid_for_cart() ) {
			return;
		}
		$coupon_type   = $coupon->get_discount_type();
		$coupon_amount = $coupon->get_amount( 'view' );
		if ( 'percent' == $coupon_type ) {
			$coupon_amount .= '%';
		} else {
			$coupon_amount = self::wc_price( $coupon_amount );
		}


		$text = str_replace( '{coupon_code}', '<span class="wapinfo-coupon-code">' . strtoupper( $coupon->get_code() ) . '</span>', $this->settings['text'] );
		$text = str_replace( '{coupon_amount}', '<span class="wapinfo-coupon-amount">' . $coupon_amount . '</span>', $text );
		?>
        <div class="wapinfo-coupon-wrap">
            <div class="wapinfo-coupon"><?php printf( '%s', wp_kses_post( $text ) ); ?></div>
            <div class="wapinfo-coupon-detail">
                <div class="wapinfo-coupon-expire"><?php if ( $coupon->get_date_expires() ) {
						// translators: %s is a placeholder for coupon expiration date.
						printf( esc_html__( 'Can only be used before %s', 'woo-advanced-product-information' ), esc_html( gmdate( 'Y-m-d', strtotime( $coupon->get_date_expires() ) ) ) );
					} ?></div>
                <div class="wapinfo-coupon-max-spend"><?php if ( $coupon->get_minimum_amount() ) {
						// translators: %s is a placeholder for coupon minimum spend amount.
						printf( esc_html__( 'Minimum spend required: %s', 'woo-advanced-product-information' ), esc_html( self::wc_price( $coupon->get_minimum_amount() ) ) );
					} ?></div>
                <div class="wapinfo-coupon-min-spend"><?php if ( $coupon->get_maximum_amount() ) {
						// translators: %1$s is a placeholder for coupon maximum spend amount.
						printf( esc_html__( 'Maximum spend is: %s', 'woo-advanced-product-information' ), esc_html( self::wc_price( $coupon->get_maximum_amount() ) ) );
					} ?></div>
            </div>
        </div>
		<?php
	}

	public static function wc_price( $price, $args = array() ) {
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
		extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
			'ex_tax_label'       => false,
			'currency'           => '',
			'decimal_separator'  => wc_get_price_decimal_separator(),
			'thousand_separator' => wc_get_price_thousand_separator(),
			'decimals'           => wc_get_price_decimals(),
			'price_format'       => get_woocommerce_price_format(),
		) ) ) );
		$unformatted_price = $price;
		$negative          = $price < 0;
		$price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * - 1 : $price ) );
		$price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

		if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
			$price = wc_trim_zeros( $price );
		}

		$formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, get_woocommerce_currency_symbol( $currency ), $price );

		return $formatted_price;
	}

	public function coupon_cart() {
		global $product;
		$coupon = new WC_Coupon( $this->settings['code'] );

		if ( ! $coupon || ( ! $coupon->is_valid_for_product( $product ) && ! $coupon->is_valid_for_cart() ) ) {
			return;
		}
		$coupon_type   = $coupon->get_discount_type();
		$coupon_amount = $coupon->get_amount( 'view' );
		if ( 'percent' == $coupon_type ) {
			$coupon_amount .= '%';
		} else {
			$coupon_amount = self::wc_price( $coupon_amount );
		}

		$text = str_replace( '{coupon_code}', '<span class="wapinfo-coupon-code">' . strtoupper( $coupon->get_code() ) . '</span>', $this->settings['text'] );
		$text = str_replace( '{coupon_amount}', '<span class="wapinfo-coupon-amount">' . $coupon_amount . '</span>', $text );
		?>
        <div class="wapinfo-coupon-wrap">
            <div class="wapinfo-coupon"><?php echo do_shortcode( $text ); ?></div>
            <div class="wapinfo-coupon-detail">
                <div class="wapinfo-coupon-expire"><?php if ( $coupon->get_date_expires() ) {
						// translators: %s is a placeholder for coupon expiration date.
						printf( esc_html__( 'Can only be used before %s', 'woo-advanced-product-information' ), esc_html( gmdate( 'Y-m-d', strtotime( $coupon->get_date_expires() ) ) ) );
					} ?></div>
                <div class="wapinfo-coupon-max-spend"><?php if ( $coupon->get_minimum_amount() ) {
						// translators: %s is a placeholder for coupon minimum spend amount.
						printf( esc_html__( 'Minimum spend required: %s', 'woo-advanced-product-information' ), esc_html( self::wc_price( $coupon->get_minimum_amount() ) ) );
					} ?></div>
                <div class="wapinfo-coupon-min-spend"><?php if ( $coupon->get_maximum_amount() ) {
						// translators: %s is a placeholder for coupon maximum spend amount.
						printf( esc_html__( 'Maximum spend is: %s', 'woo-advanced-product-information' ), esc_html( self::wc_price( $coupon->get_maximum_amount() ) ) );
					} ?></div>
            </div>
        </div>
		<?php
	}

}