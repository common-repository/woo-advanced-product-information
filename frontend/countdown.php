<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Countdown {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
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
		), $data->get_params( 'countdown' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		if ( ( is_product() && is_single() ) || is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) ) {
			global $post;

			if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
				return;
			}
			if ( ! $this->condition( $post->ID ) ) {
				return;
			}
			wp_enqueue_script( 'wapinfo-frontend-countdown-javascript', VI_WC_ADVANCED_PRODUCT_INFORMATION_JS . 'frontend/countdown.js', array( 'jquery' ), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION, true );
			wp_enqueue_style( 'wapinfo-frontend-countdown-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/countdown.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			$css = '';
			if ( $this->settings['text_align'] ) {
				$css .= '.wapinfo-shortcode-wrap-wrap{text-align:' . $this->settings['text_align'] . ';}';
			}
			$css .= '.wapinfo-shortcode-countdown-1{';
			if ( $this->settings['border_color'] ) {
				$css .= 'border:1px solid ' . $this->settings['border_color'] . ';';
			}
			if ( $this->settings['border_radius'] ) {
				$css .= 'border-radius:' . $this->settings['border_radius'] . 'px;';
			}
			if ( $this->settings['text_color'] ) {
				$css .= 'color:' . $this->settings['text_color'] . ';';
			}
			if ( $this->settings['text_bg_color'] ) {
				$css .= 'background-color:' . $this->settings['text_bg_color'] . ';';
			}

			if ( in_array( $this->settings['position'], array(
				'before_price',
				'after_price'
			), true ) ) {
				$css .= 'display:block;';
			}
			$css .= '}';
			if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
				$css .= $this->settings['css'];
			}
			wp_add_inline_style( 'wapinfo-frontend-countdown-style', $css );
			switch ( $this->settings['position'] ) {
				case 'before_saleflash':
				case 'before_price':
					add_action( 'woocommerce_before_template_part', array( $this, 'countdown' ), 11 );
					break;
				case 'after_saleflash':
				case 'after_price':
					add_action( 'woocommerce_after_template_part', array( $this, 'countdown' ), 11 );
					break;
			}
		}
	}

	public function condition( $product_id ) {
		$product = wc_get_product( $product_id );
		$return  = false;
		if ( ! $product ) {
			return false;
		}
		$product_cates = $product->get_category_ids();

		if ( count( $this->settings['include_product'] ) && in_array( strval( $product_id ), $this->settings['include_product'], true ) ) {
			$return = true;
		}
		if ( count( $this->settings['include_category'] ) && count( $product_cates ) && count( array_intersect( $product_cates, $this->settings['include_category'] ) ) ) {
			$return = true;
		}
		if ( ! count( $this->settings['include_product'] ) && ! count( $this->settings['include_category'] ) ) {
			$return = true;
		}
		if ( count( $this->settings['exclude_product'] ) && in_array( strval( $product_id ), $this->settings['exclude_product'], true ) ) {
			$return = false;
		}

		if ( count( $this->settings['exclude_category'] ) && count( $product_cates ) && count( array_intersect( $product_cates, $this->settings['exclude_category'] ) ) ) {
			$return = false;
		}

		return $return;
	}

	public function countdown( $template_name ) {
		global $product;
		if ( in_array( $this->settings['position'], array(
				'before_saleflash',
				'after_saleflash'
			), true ) && ! in_array( $template_name, array( 'single-product/sale-flash.php', 'loop/sale-flash.php' ), true ) ) {
			return;
		}
		if ( in_array( $this->settings['position'], array(
				'before_price',
				'after_price'
			), true ) && ! in_array( $template_name, array( 'single-product/price.php', 'loop/price.php' ), true ) ) {
			return;
		}

		if ( ! $product->is_on_sale() ) {
			return;
		}

		$text_before = $text_after = '';
		$text        = explode( '{countdown_timer}', $this->settings['text'] );
		if ( count( $text ) < 2 ) {
			return;
		}
		$text_before = $text[0];
		$text_after  = $text[1];
		$day         = $hour = $min = $second = '';
		switch ( $this->settings['style'] ) {
			case '1':
				$day    = 'd';
				$hour   = 'h';
				$min    = 'm';
				$second = 's';
				break;
			case '2':
				$day    = 'days';
				$hour   = 'hrs';
				$min    = 'mins';
				$second = 'secs';
				break;
			case '3':
				$day    = 'days';
				$hour   = 'hours';
				$min    = 'minutes';
				$second = 'seconds';
				break;
			default:
				$day    = '';
				$hour   = '';
				$min    = '';
				$second = '';
		}
		$now      = time();
		$end_time = 0;
		if ( ! $product->get_date_on_sale_to() ) {
			if ( $this->settings['fake'] ) {
				$end_time = strtotime( 'tomorrow' );
			}
		} else {
			$end_time = $product->get_date_on_sale_to()->getTimestamp();
		}
		$end_time = $end_time - $now;
		if ( $end_time > 0 ) {
			if ( 1 == $this->settings['type'] ) {
				?>
                <div class="wapinfo-shortcode-wrap-wrap wapinfo-shortcode-type-1">
                    <input type="hidden" class="wapinfo-shortcode-data-end_time"
                           value="<?php echo esc_attr( $end_time ); ?>">

                    <span class="wapinfo-shortcode-countdown-text-before"><?php echo do_shortcode( $text_before ); ?></span>
                    <div class="wapinfo-shortcode-countdown-1">
                        <span class="wapinfo-shortcode-countdown-text-top"></span>
                        <div class="wapinfo-shortcode-countdown-2">
                                <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-date wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-date-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-date-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $day ); ?></span>
                                </span>
                                </span>
                            <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                            <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-hour wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-hour-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-hour-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $hour ); ?></span>
                                </span>
                                </span>
                            <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                            <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-minute wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-minute-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-minute-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $min ); ?></span>
                                </span>
                                </span>
                            <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                            <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-second wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-second-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-second-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $second ); ?></span>
                                </span>
                                </span>
                        </div>
                    </div>
                    <span class="wapinfo-shortcode-countdown-text-after"><?php echo do_shortcode( $text_after ); ?></span>

                </div>
				<?php
			} else {
				?>
                <div class="wapinfo-shortcode-wrap-wrap">
                    <div class="wapinfo-shortcode-wrap">
                        <input type="hidden" class="wapinfo-shortcode-data-end_time"
                               value="<?php echo esc_attr( $end_time ); ?>">
                        <div class="wapinfo-shortcode-countdown-wrap wapinfo-shortcode-countdown-style-3">
                            <div class="wapinfo-shortcode-countdown">
                                <span class="wapinfo-shortcode-countdown-text-before"><?php echo do_shortcode( $text_before ); ?></span>
                                <div class="wapinfo-shortcode-countdown-1">
                                    <span class="wapinfo-shortcode-countdown-text-top"></span>
                                    <div class="wapinfo-shortcode-countdown-2">
                                <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-date wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-date-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-date-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $day ); ?></span>
                                </span>
                                </span>
                                        <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                                        <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-hour wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-hour-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-hour-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $hour ); ?></span>
                                </span>
                                </span>
                                        <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                                        <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-minute wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-minute-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-minute-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $min ); ?></span>
                                </span>
                                </span>
                                        <span class="wapinfo-shortcode-countdown-time-separator">:</span>
                                        <span class="wapinfo-shortcode-countdown-unit-wrap">
                                <span class="wapinfo-shortcode-countdown-second wapinfo-shortcode-countdown-unit">
                                    <span class="wapinfo-shortcode-countdown-second-value wapinfo-shortcode-countdown-value"></span>
                                    <span class="wapinfo-shortcode-countdown-second-text wapinfo-shortcode-countdown-text"><?php echo esc_html( $second ); ?></span>
                                </span>
                                </span>
                                    </div>
                                </div>
                                <span class="wapinfo-shortcode-countdown-text-after"><?php echo do_shortcode( $text_after ); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
		}
	}
}