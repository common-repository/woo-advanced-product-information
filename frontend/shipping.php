<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Shipping {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = $data->get_params( 'shipping' );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-shipping-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/shipping.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-shipping-style', $css );
		add_action( 'woocommerce_single_product_summary', array( $this, 'shipping' ), 99 );
	}

	public function shipping() {
		$available    = '';
		$geolocation  = new WC_Geolocation();
		$geo_ip       = $geolocation->geolocate_ip();
		$country_code = isset( $geo_ip['country'] ) ? $geo_ip['country'] : '';
		if ( ! $country_code ) {
			return;
		}
		$zone = new WC_Shipping_Zone_Data_Store();
		if ( is_array( $zone->get_methods( 0, true ) ) && count( $zone->get_methods( 0, true ) ) ) {
//	        Locations not covered by your other zones
			$available = 1;
		} elseif ( is_array( $zone->get_zones() ) && count( $zone->get_zones() ) ) {
//            Shipping zones
			foreach ( $zone->get_zones() as $z ) {
				if ( is_array( $zone->get_methods( $z->zone_id, true ) ) && count( $zone->get_methods( $z->zone_id, true ) ) ) {
					$shipping_zone = new WC_Shipping_Zone( $z->zone_id );
					$locations     = $shipping_zone->get_zone_locations();
					foreach ( $locations as $location ) {
						if ( $country_code == $location->code ) {
							$available = 1;
							break;
						}
					}
				}
			}
		}
		$text = esc_html__( 'Shipping is not available ', 'woo-advanced-product-information' );
		if ( $available ) {
			$text = esc_html__( 'Shipping is available ', 'woo-advanced-product-information' );
		}
		?>
        <div class="wapinfo-shipping-availability">
            <span class="wapinfo-shipping-availability-info"><?php echo esc_html($text) . esc_html__( 'in ', 'woo-advanced-product-information' ) . '<span>' . esc_html(WC()->countries->countries[ $country_code ]) . '</span>.'; ?></span>
        </div>
		<?php
	}
}