<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Custom {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'     => "on",
			'mobile'     => "on",
			'layout'     => "2",
			'position'   => "after_meta",
			'icon_width' => '',
			'icon_color' => '',
			'text_color' => '',
			'background' => '',
			'css'        => '',
			'row'        => array(
				'icon'    => array(),
				'heading' => array(),
				'text'    => array(),
				'url'     => array(),
			)
		), $data->get_params( 'custom' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
		if ( ! is_array( $this->settings['row']['icon'] ) || ! count( $this->settings['row']['icon'] ) ) {
			return;
		}
		if ( ! is_product() || ! is_single() ) {
			return;
		}
		wp_enqueue_style( 'wapinfo-frontend-custom-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/custom.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
		$css = '';
		if ( isset( $this->settings['layout'] ) && 2 == $this->settings['layout'] ) {
			$css .= '';
		} else {
			$css .= '.wapinfo-custom-policy-content-container{grid-template-columns: repeat(auto-fill,minmax(calc(100%/' . count( $this->settings['row']['icon'] ) . '),1fr));}';
		}
		if ( $this->settings['font_size'] ) {
			$css .= '.wapinfo-custom-policy-wrap{font-size:' . $this->settings['font_size'] . 'px;}';
		}
		if ( $this->settings['icon_width'] ) {
			$css .= '.wapinfo-custom-policy-icon span:before{font-size:' . $this->settings['icon_width'] . 'px;line-height:' . $this->settings['icon_width'] . 'px;}';
		}
		if ( $this->settings['background'] ) {
			$css .= '.wapinfo-custom-policy-content-container{background:' . $this->settings['background'] . ';}';
		}
		if ( $this->settings['icon_color'] ) {
			$css .= '.wapinfo-custom-policy-icon>span{color:' . $this->settings['icon_color'] . ';}';
		}

		if ( $this->settings['text_color'] ) {
			$css .= '.wapinfo-custom-policy-text{color:' . $this->settings['text_color'] . ';}';
		}

		if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
			$css .= $this->settings['css'];
		}
		wp_add_inline_style( 'wapinfo-frontend-custom-style', $css );
		switch ( $this->settings['position'] ) {
			case 'before_cart':
				add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'custom' ) );
				break;
			case 'after_cart':
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'custom' ) );
				break;
			case 'before_meta':
				add_action( 'woocommerce_product_meta_start', array( $this, 'custom' ) );
				break;
			case 'before_footer':
				add_action( 'get_footer', array( $this, 'custom' ) );
				break;
			default:
				add_action( 'woocommerce_product_meta_end', array( $this, 'custom' ) );
		}
	}

	public function custom() {
		$rows = $this->settings['row'];
		if ( is_array( $rows['text'] ) && count( $rows['text'] ) ) {

			?>
            <div class="wapinfo-custom-policy-wrap wapinfo-custom-policy-position-<?php echo esc_attr( $this->settings['position'] ) ?>">
                <div class="wapinfo-custom-policy-content-container <?php echo esc_attr( 2 == $this->settings['layout'] ? 'wapinfo-layout-2' : '' ) ?>">
					<?php
					for ( $i = 0; $i < sizeof( $rows['text'] ); $i ++ ) {
						if ( ! $rows['text'][ $i ] || ! $rows['icon'][ $i ] ) {
							continue;
						}
						?>
                        <div class="wapinfo-custom-policy-column">
                            <div class="wapinfo-custom-policy-icon">
                                <span class="<?php echo esc_attr( wapi_get_icon( $rows['icon'][ $i ] ) ) ?>"></span>
                            </div>
                            <div class="wapinfo-custom-policy-text">
								<?php
								if ( isset( $rows['heading'][ $i ] ) ) {
									?>
                                    <span class="wapinfo-custom-table-heading"><?php echo esc_html( $rows['heading'][ $i ] ) ?></span>
									<?php
								}
								?>
                                <p><?php echo ( isset( $rows['url'][ $i ] ) && wc_is_valid_url( $rows['url'][ $i ] ) ) ? '<a href="' . esc_url( $rows['url'][ $i ] ) . '" target="_blank">' . esc_html( $rows['text'][ $i ] ) . '</a>' : esc_html( $rows['text'][ $i ] ); ?></p>
                            </div>
                        </div>
						<?php
					}
					?>
                </div>
            </div>
			<?php

		}
	}

}