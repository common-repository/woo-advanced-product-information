<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Payment {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'        => 'on',
			'mobile'        => "on",
			'position'      => "after_meta",
			'border_radius' => '',
			'id'            => array(),
			'icon'          => array(),
			'url'           => array(),
			'active'        => array(),
			'css'           => '',
		), $data->get_params( 'payment' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );

	}

	public function frontend_enqueue() {
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-payment-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/payment.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-payment-style', $css );
		switch ( $this->settings['position'] ) {
			case 'before_meta':
				add_action( 'woocommerce_product_meta_start', array( $this, 'payment' ), 10 );
				break;
			default:
				add_action( 'woocommerce_product_meta_end', array( $this, 'payment' ), 10 );
		}
	}

	public function payment() {
		if ( ! is_single() || ! is_product() ) {
			return;
		}
		if ( is_array( $this->settings['id'] ) && $payments = count( $this->settings['id'] ) ) {
			?>
            <div class="wapinfo-payment-method-wrap">
				<?php
				for (
					$i = 0;
					$i < $payments;
					$i ++
				) {
					if ( ! $this->settings['active'][ $i ] ) {
						continue;
					}
					if ( wc_is_valid_url( $this->settings['icon'][ $i ] ) ) {
						?>
                        <div class="wapinfo-payment-method wapinfo-payment-method-<?php echo esc_attr( $this->settings['id'][ $i ] ) ?>"
                             title="<?php echo esc_attr( $this->settings['id'][ $i ] ); ?>">
							<?php
							if ( wc_is_valid_url( $this->settings['url'][ $i ] ) ) {
								?>
                                <a href="<?php echo esc_url( $this->settings['url'][ $i ] ); ?>" target="_blank">
                                    <img src="<?php echo esc_attr( $this->settings['icon'][ $i ] ) ?>">
                                </a>
								<?php
							} else {
								?>
                                <img src="<?php echo esc_attr( $this->settings['icon'][ $i ] ) ?>">
								<?php
							}
							?>
                        </div>
						<?php
					} elseif ( has_shortcode( $this->settings['icon'][ $i ], 'wapi_icon' ) ) {
						?>
                        <div class="wapinfo-payment-method wapinfo-payment-method-<?php echo esc_attr( $this->settings['id'][ $i ] ) ?>"
                             title="<?php echo esc_attr( $this->settings['id'][ $i ] ); ?>">
							<?php
							if ( wc_is_valid_url( $this->settings['url'][ $i ] ) ) {
								?>
                                <a href="<?php echo esc_url( $this->settings['url'][ $i ] ); ?>" target="_blank">
									<?php
									echo do_shortcode( $this->settings['icon'][ $i ] );
									?>
                                </a>
								<?php
							} else {
								echo do_shortcode( $this->settings['icon'][ $i ] );
							}
							?>
                        </div>
						<?php
					} else {

					}
				}
				?>
            </div>
			<?php
		}
	}

}