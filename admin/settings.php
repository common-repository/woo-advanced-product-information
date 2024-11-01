<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Admin_Settings {
	static $wapi_settings_admin;

	public function __construct() {
		add_action( 'admin_init', array( $this, 'save_settings' ) );
		add_action( 'wp_ajax_wapi_search_coupon', array( $this, 'search_coupon' ) );
		add_action( 'wp_ajax_wapi_search_product', array( $this, 'search_product' ) );
		add_action( 'wp_ajax_wapi_search_cate', array( $this, 'search_cate' ) );
	}

	public static function search_coupon() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		ob_start();
		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
			'post_status'    => 'publish',
			'post_type'      => 'shop_coupon',
			'posts_per_page' => 50,
			's'              => $keyword,
			// @codingStandardsIgnoreLine
			'meta_query'     => array(
				'ralation' => 'AND',
				array(
					'key'     => 'wlwl_unique_coupon',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key'     => 'kt_unique_coupon',
					'compare' => 'NOT EXISTS'
				),
			)
		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$coupon = new WC_Coupon( get_the_ID() );
				if ( $coupon->get_usage_limit() > 0 && $coupon->get_usage_count() >= $coupon->get_usage_limit() ) {
					continue;
				}
				if ( $coupon->get_amount() < 1 ) {
					continue;
				}
				if ( $coupon->get_date_expires() && time() > $coupon->get_date_expires()->getTimestamp() ) {
					continue;
				}
				$product          = array( 'id' => get_the_ID(), 'text' => get_the_title() );
				$found_products[] = $product;
			}
		}
		wp_reset_postdata();
		wp_send_json( $found_products );
	}

	/*Ajax Product Search*/

	public static function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$wapi_settings_admin = get_option( '_wapi_settings', array() );
		?>
        <div class="wrap">
            <h2><?php esc_html_e( 'Advanced Product Information for WooCommerce Settings', 'woo-advanced-product-information' ); ?></h2>
            <div class="vi-ui menu vertical pointing">
                <a class="item <?php if ( ! isset( $wapi_settings_admin['review']['enable'] ) || 'on' !== $wapi_settings_admin['review']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="review"><?php esc_html_e( 'Review Info', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['instock']['enable'] ) || 'on' !== $wapi_settings_admin['instock']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="instock"><?php esc_html_e( 'Instock Info', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['shipping']['enable'] ) || 'on' !== $wapi_settings_admin['shipping']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="shipping"><?php esc_html_e( 'Shipping availability', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['sale']['enable'] ) || 'on' !== $wapi_settings_admin['sale']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="sale"><?php esc_html_e( 'Sale Saves', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['countdown']['enable'] ) || 'on' !== $wapi_settings_admin['countdown']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="countdown"><?php esc_html_e( 'Sale Countdown', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['recent']['enable'] ) || 'on' !== $wapi_settings_admin['recent']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="recent"><?php esc_html_e( 'Sales count', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['rank']['enable'] ) || 'on' !== $wapi_settings_admin['rank']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="rank"><?php esc_html_e( 'Rank In Category', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['payment']['enable'] ) || 'on' !== $wapi_settings_admin['payment']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="payment"><?php esc_html_e( 'Payments', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['social_proof']['enable'] ) || 'on' !== $wapi_settings_admin['social_proof']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="social_proof"><?php esc_html_e( 'Social Proof', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['coupon']['enable'] ) || 'on' !== $wapi_settings_admin['coupon']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="coupon"><?php esc_html_e( 'Available Coupon', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['custom']['enable'] ) || 'on' !== $wapi_settings_admin['custom']['enable'] )
					echo 'wapi-inactive-item' ?>"
                   data-tab="custom"><?php esc_html_e( 'Custom Policy', 'woo-advanced-product-information' ); ?></a>
                <a class="item <?php if ( ! isset( $wapi_settings_admin['orders']['enable'] ) || 'on' !== $wapi_settings_admin['orders']['enable']  )
					echo 'wapi-inactive-item' ?>"
                   data-tab="orders"><?php esc_html_e( 'Recent Orders', 'woo-advanced-product-information' ); ?></a>
            </div>
            <form action="" method="POST" class="vi-ui form">
				<?php wp_nonce_field( 'wapi_settings_page_save', 'wapi_nonce_field' ); ?>

                <div class="vi-ui tab segment" data-tab="review">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-review"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_review" class="wapi-checkbox-enable"
                                           id="wapi-enable-review" <?php checked( $wapi_settings_admin['review']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="no-review"><?php esc_html_e( 'Text displayed when there\'s no review', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="no_review" id="no-review"
                                       value="<?php if ( $wapi_settings_admin['review']['no_review'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['review']['no_review'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="satisfied-reviews"><?php esc_html_e( 'Satisfied Rate', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="satisfied_reviews" id="satisfied-reviews"
                                       value="<?php if ( $wapi_settings_admin['review']['satisfied'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['review']['satisfied'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="satisfied-min"><?php esc_html_e( 'Min rate to display', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" name="satisfied_min" id="satisfied-min"
                                           min="0" value="<?php if ( $wapi_settings_admin['review']['min_rate'] ) {
										echo esc_attr( $wapi_settings_admin['review']['min_rate'] );
									} ?>">%
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="review_text_align" id="review-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['review']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['review']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['review']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['review']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="review-border-color"
                                       name="review_border_color"
                                       value="<?php if ( $wapi_settings_admin['review']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['review']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['review']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['review']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="review-border-radius" min="0"
                                           name="review_border_radius"
                                           value="<?php if ( $wapi_settings_admin['review']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['review']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="review_text_color" id="review-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['review']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['review']['text_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['review']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['review']['text_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="review_text_bg_color" id="review-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['review']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['review']['text_bg_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['review']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['review']['text_bg_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="review_css"
                                          id="review-css"><?php echo ( isset( $wapi_settings_admin['review']['css'] ) && $wapi_settings_admin['review']['css'] ) ? esc_textarea( $wapi_settings_admin['review']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="review_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['review']['include_product'] ) && is_array( $wapi_settings_admin['review']['include_product'] ) && count( $wapi_settings_admin['review']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['review']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="review_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['review']['exclude_product'] ) && is_array( $wapi_settings_admin['review']['exclude_product'] ) && count( $wapi_settings_admin['review']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['review']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="review_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['review']['include_category'] ) && is_array( $wapi_settings_admin['review']['include_category'] ) && count( $wapi_settings_admin['review']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['review']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="review-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="review_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['review']['exclude_category'] ) && is_array( $wapi_settings_admin['review']['exclude_category'] ) && count( $wapi_settings_admin['review']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['review']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui   tab segment" data-tab="instock">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-instock"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_instock" class="wapi-checkbox-enable"
                                           id="wapi-enable-instock" <?php checked( $wapi_settings_admin['instock']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="instock-text"><?php esc_html_e( 'Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="instock_text" id="instock-text"
                                       value="<?php if ( $wapi_settings_admin['instock']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['instock']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>{instock_quantity}
                                - <?php esc_html_e( 'The quantity of product left in stock', 'woo-advanced-product-information' ); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Quantity', 'woo-advanced-product-information' ); ?></th>
                            <td>
                                <div class="grouped fields">
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="wapi_instock" id="wapi-instock-real"
                                                   value="0" <?php checked( $wapi_settings_admin['instock']['fake'], 0 ); ?>><label
                                                    for="wapi-instock-real"><?php esc_html_e( 'Real', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="wapi_instock" id="wapi-instock-fake"
                                                   value="1" <?php checked( $wapi_settings_admin['instock']['fake'], 1 ); ?>><label
                                                    for="wapi-instock-fake"><?php esc_html_e( 'Virtual', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Virtual Value', 'woo-advanced-product-information' ); ?></th>
                            <td>
                                <div class="equal width fields">
                                    <div class="field">
                                        <label for="instock-minrand"><?php esc_html_e( 'Minrand:', 'woo-advanced-product-information' ); ?></label><input
                                                type="number" name="instock_minrand" id="instock-minrand"
                                                min="0" step="1"
                                                value="<?php if ( $wapi_settings_admin['instock']['minrand'] ) {
													echo esc_attr( $wapi_settings_admin['instock']['minrand'] );
												} ?>">
                                    </div>
                                    <div class="field">
                                        <label for="instock-maxrand"><?php esc_html_e( 'Maxrand:', 'woo-advanced-product-information' ); ?></label><input
                                                type="number" name="instock_maxrand" id="instock-maxrand"
                                                min="0" step="1"
                                                value="<?php if ( $wapi_settings_admin['instock']['maxrand'] ) {
													echo esc_attr( $wapi_settings_admin['instock']['maxrand'] );
												} ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_position" id="instock-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['instock']['position'] ) && 'before_meta' == $wapi_settings_admin['instock']['position'] ); ?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['instock']['position'] ) && 'after_meta' == $wapi_settings_admin['instock']['position'] ) ?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_cart" <?php selected( isset( $wapi_settings_admin['instock']['position'] ) && 'before_cart' == $wapi_settings_admin['instock']['position'] ); ?>><?php esc_html_e( 'Before add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['instock']['position'] ) && 'after_cart' == $wapi_settings_admin['instock']['position'] ); ?>><?php esc_html_e( 'After add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="instock-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_text_align" id="instock-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['instock']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['instock']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['instock']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['instock']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="instock-border-color"
                                       name="instock_border_color"
                                       value="<?php if ( $wapi_settings_admin['instock']['border_color'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['instock']['border_color'] ) );
								       } ?>" style="<?php if ( $wapi_settings_admin['instock']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['instock']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="instock-border-radius" min="0"
                                           name="instock_border_radius"
                                           value="<?php if ( $wapi_settings_admin['instock']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['instock']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="instock_text_color" id="instock-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['instock']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['instock']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="instock_text_bg_color" id="instock-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['instock']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['text_bg_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['instock']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['text_bg_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-style"><?php esc_html_e( 'Style', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_style" id="instock-style">
                                    <option value="1" <?php selected( isset( $wapi_settings_admin['instock']['style'] ) && '1' == $wapi_settings_admin['instock']['style'] ); ?>><?php esc_html_e( 'Only text', 'woo-advanced-product-information' ); ?></option>
                                    <option value="2" <?php selected( isset( $wapi_settings_admin['instock']['style'] ) && '2' == $wapi_settings_admin['instock']['style'] ); ?>><?php esc_html_e( 'Count bar loading', 'woo-advanced-product-information' ); ?></option>
                                    <option value="3" <?php selected( isset( $wapi_settings_admin['instock']['style'] ) && '3' == $wapi_settings_admin['instock']['style'] ); ?>><?php esc_html_e( 'Count bar plain', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr class="instock-count-bar-style <?php if ( isset( $wapi_settings_admin['instock']['style'] ) && '1' == $wapi_settings_admin['instock']['style'] )
							echo 'instock-count-bar-style-hide' ?>">
                            <th>
                                <label for="instock-bar-color-1"><?php esc_html_e( 'Bar color 1', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="instock_bar_color_1" id="instock-bar-color-1" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['instock']['bar_color1'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['bar_color1'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['instock']['bar_color1'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['bar_color1'] );
								       } ?> ;"/>
                            </td>
                        </tr>

                        <tr class="instock-count-bar-style <?php if ( isset( $wapi_settings_admin['instock']['style'] ) && '1' == $wapi_settings_admin['instock']['style'] )
							echo 'instock-count-bar-style-hide' ?>">
                            <th>
                                <label for="instock-bar-color-2"><?php esc_html_e( 'Bar color 2', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="instock_bar_color_2" id="instock-bar-color-2" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['instock']['bar_color2'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['bar_color2'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['instock']['bar_color2'] ) {
									       echo esc_attr( $wapi_settings_admin['instock']['bar_color2'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr class="instock-count-bar-style <?php if ( isset( $wapi_settings_admin['instock']['style'] ) && '1' == $wapi_settings_admin['instock']['style'] )
							echo 'instock-count-bar-style-hide' ?>">
                            <th>
                                <label><?php esc_html_e( 'Bar width', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="equal width fields">
                                    <label for="instock-bar-width-min"><?php esc_html_e( 'Min', 'woo-advanced-product-information' ); ?></label>
                                    <div class="inline field">
                                        <input name="instock_bar_width_min" id="instock-bar-width-min" type="number"
                                               min="1"
                                               max="100"
                                               value="<?php if ( $wapi_settings_admin['instock']['width_min'] ) {
											       echo esc_attr( $wapi_settings_admin['instock']['width_min'] );
										       } ?>"/><?php esc_html_e( '%', 'woo-advanced-product-information' ); ?>
                                    </div>
                                    <label for="instock-bar-width-max"><?php esc_html_e( 'Max', 'woo-advanced-product-information' ); ?></label>
                                    <div class="inline field">
                                        <input name="instock_bar_width_max" id="instock-bar-width-max" type="number"
                                               min="1"
                                               max="100"
                                               value="<?php if ( $wapi_settings_admin['instock']['width_max'] ) {
											       echo esc_attr( $wapi_settings_admin['instock']['width_max'] );
										       } ?>"/><?php esc_html_e( '%', 'woo-advanced-product-information' ); ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="instock_css"
                                          id="instock-css"><?php echo ( isset( $wapi_settings_admin['instock']['css'] ) && $wapi_settings_admin['instock']['css'] ) ? esc_textarea( $wapi_settings_admin['instock']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['instock']['include_product'] ) && is_array( $wapi_settings_admin['instock']['include_product'] ) && count( $wapi_settings_admin['instock']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['instock']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['instock']['exclude_product'] ) && is_array( $wapi_settings_admin['instock']['exclude_product'] ) && count( $wapi_settings_admin['instock']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['instock']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['instock']['include_category'] ) && is_array( $wapi_settings_admin['instock']['include_category'] ) && count( $wapi_settings_admin['instock']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['instock']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="instock-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="instock_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['instock']['exclude_category'] ) && is_array( $wapi_settings_admin['instock']['exclude_category'] ) && count( $wapi_settings_admin['instock']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['instock']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui tab segment" data-tab="shipping">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-shipping"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_shipping" class="wapi-checkbox-enable"
                                           id="wapi-enable-shipping" <?php checked( $wapi_settings_admin['shipping']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="shipping-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="shipping_css"
                                          id="shipping-css"><?php echo ( isset( $wapi_settings_admin['shipping']['css'] ) && $wapi_settings_admin['shipping']['css'] ) ? esc_textarea( $wapi_settings_admin['shipping']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui tab segment" data-tab="sale">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-sale"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_sale" class="wapi-checkbox-enable"
                                           id="wapi-enable-sale" <?php checked( $wapi_settings_admin['sale']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="sale-text"><?php esc_html_e( 'Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="sale_text" id="sale-text"
                                       value="<?php if ( $wapi_settings_admin['sale']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['sale']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                                <p><?php esc_html_e( '{sale_amount} - The amount of money which will be saved if buy when a product is on sale', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '{sale_percent} - Percentage of money which will be saved if buy when a product is on sale', 'woo-advanced-product-information' ); ?></p>
                            </td>
                        </tr>
                        <tr class="wapi-settings">
                            <th>
                                <label for="sale-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td colspan="3">
                                <select name="sale_position" id="sale-position">
                                    <option value="after_price" <?php selected( isset( $wapi_settings_admin['sale']['position'] ) && 'after_price' == $wapi_settings_admin['sale']['position'] ); ?>><?php esc_html_e( 'After product price', 'woo-advanced-product-information' ); ?></option>
                                    <option value="saleflash" <?php selected( isset( $wapi_settings_admin['sale']['position'] ) && 'saleflash' == $wapi_settings_admin['sale']['position'] ); ?>><?php esc_html_e( 'Saleflash', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="sale_text_align" id="sale-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['sale']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['sale']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['sale']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['sale']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="sale-border-color"
                                       name="sale_border_color"
                                       value="<?php if ( $wapi_settings_admin['sale']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['sale']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['sale']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['sale']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="sale-border-radius" min="0"
                                           name="sale_border_radius"
                                           value="<?php if ( $wapi_settings_admin['sale']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['sale']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="sale_text_color" id="sale-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['sale']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['sale']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['sale']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['sale']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="sale_text_bg_color" id="sale-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['sale']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['sale']['text_bg_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['sale']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['sale']['text_bg_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="sale_css"
                                          id="sale-css"><?php echo ( isset( $wapi_settings_admin['sale']['css'] ) && $wapi_settings_admin['sale']['css'] ) ? esc_textarea( $wapi_settings_admin['sale']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="sale_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['sale']['include_product'] ) && is_array( $wapi_settings_admin['sale']['include_product'] ) && count( $wapi_settings_admin['sale']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['sale']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="sale_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['sale']['exclude_product'] ) && is_array( $wapi_settings_admin['sale']['exclude_product'] ) && count( $wapi_settings_admin['sale']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['sale']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="sale_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['sale']['include_category'] ) && is_array( $wapi_settings_admin['sale']['include_category'] ) && count( $wapi_settings_admin['sale']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['sale']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="sale-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="sale_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['sale']['exclude_category'] ) && is_array( $wapi_settings_admin['sale']['exclude_category'] ) && count( $wapi_settings_admin['sale']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['sale']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui   tab segment" data-tab="countdown">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-countdown"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_countdown" class="wapi-checkbox-enable"
                                           id="wapi-enable-countdown" <?php checked( $wapi_settings_admin['countdown']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th><?php esc_html_e( 'Loop', 'woo-advanced-product-information' ); ?></th>
                            <td>

                                <div class="field">
                                    <div class="vi-ui toggle checkbox">
                                        <input type="checkbox" name="wapi_countdown" id="wapi-countdown"
                                               value="1" <?php checked( $wapi_settings_admin['countdown']['fake'], 1 ); ?>><label
                                                for="wapi-countdown"><?php esc_html_e( 'Loop countdown for sale products without scheduled time', 'woo-advanced-product-information' ); ?></label>
                                    </div>
                                </div>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-text"><?php esc_html_e( 'Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" id="countdown-text"
                                       name="countdown_text"

                                       value="<?php if ( isset( $wapi_settings_admin['countdown']['text'] ) && $wapi_settings_admin['countdown']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['countdown']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-style"><?php esc_html_e( 'Time format', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_style" id="countdown-style">
                                    <option value="1" <?php selected( isset( $wapi_settings_admin['countdown']['style'] ) && '1' == $wapi_settings_admin['countdown']['style'] ); ?>><?php esc_html_e( '01d 02h 03m 04s', 'woo-advanced-product-information' ); ?></option>
                                    <option value="2" <?php selected( isset( $wapi_settings_admin['countdown']['style'] ) && '2' == $wapi_settings_admin['countdown']['style'] ); ?>><?php esc_html_e( '01days 02hrs 03mins 04secs', 'woo-advanced-product-information' ); ?></option>
                                    <option value="3" <?php selected( isset( $wapi_settings_admin['countdown']['style'] ) && '3' == $wapi_settings_admin['countdown']['style'] ); ?>><?php esc_html_e( '01day 02hour 03minute 04second', 'woo-advanced-product-information' ); ?></option>
                                    <option value="4" <?php selected( isset( $wapi_settings_admin['countdown']['style'] ) && '4' == $wapi_settings_admin['countdown']['style'] ); ?>><?php esc_html_e( '01:02:03:04', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-type"><?php esc_html_e( 'Display type', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_type" id="countdown-type">
                                    <option value="1" <?php selected( isset( $wapi_settings_admin['countdown']['type'] ) && '1' == $wapi_settings_admin['countdown']['type'] ); ?>><?php esc_html_e( 'Plain text', 'woo-advanced-product-information' ); ?></option>
                                    <option value="2" <?php selected( isset( $wapi_settings_admin['countdown']['type'] ) && '2' == $wapi_settings_admin['countdown']['type'] ); ?>><?php esc_html_e( 'Block', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_position" id="countdown-position">
                                    <option value="before_saleflash" <?php selected( isset( $wapi_settings_admin['countdown']['position'] ) && 'before_saleflash' == $wapi_settings_admin['countdown']['position'] ); ?>><?php esc_html_e( 'Before sale flash', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_saleflash" <?php selected( isset( $wapi_settings_admin['countdown']['position'] ) && 'after_saleflash' == $wapi_settings_admin['countdown']['position'] ); ?>><?php esc_html_e( 'After sale flash', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_price" <?php selected( isset( $wapi_settings_admin['countdown']['position'] ) && 'before_price' == $wapi_settings_admin['countdown']['position'] ); ?>><?php esc_html_e( 'Before price', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_price" <?php selected( isset( $wapi_settings_admin['countdown']['position'] ) && 'after_price' == $wapi_settings_admin['countdown']['position'] ); ?>><?php esc_html_e( 'After price', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_text_align" id="countdown-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['countdown']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['countdown']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['countdown']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['countdown']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="countdown-border-color"
                                       name="countdown_border_color"

                                       value="<?php if ( $wapi_settings_admin['countdown']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['countdown']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['countdown']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['countdown']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="countdown-border-radius" min="0"
                                           name="countdown_border_radius"
                                           value="<?php if ( $wapi_settings_admin['countdown']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['countdown']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="countdown_text_color" id="countdown-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['countdown']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['countdown']['text_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['countdown']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['countdown']['text_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="countdown_text_bg_color" id="countdown-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['countdown']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['countdown']['text_bg_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['countdown']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['countdown']['text_bg_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="countdown_css"
                                          id="countdown-css"><?php echo ( isset( $wapi_settings_admin['countdown']['css'] ) && $wapi_settings_admin['countdown']['css'] ) ? esc_textarea( $wapi_settings_admin['countdown']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['countdown']['include_product'] ) && is_array( $wapi_settings_admin['countdown']['include_product'] ) && count( $wapi_settings_admin['countdown']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['countdown']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['countdown']['exclude_product'] ) && is_array( $wapi_settings_admin['countdown']['exclude_product'] ) && count( $wapi_settings_admin['countdown']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['countdown']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['countdown']['include_category'] ) && is_array( $wapi_settings_admin['countdown']['include_category'] ) && count( $wapi_settings_admin['countdown']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['countdown']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="countdown-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="countdown_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['countdown']['exclude_category'] ) && is_array( $wapi_settings_admin['countdown']['exclude_category'] ) && count( $wapi_settings_admin['countdown']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['countdown']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui   tab segment" data-tab="recent">
                    <table class="form-table">
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-recent-order"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_recent_order" class="wapi-checkbox-enable"
                                           id="wapi-enable-recent-order" <?php checked( $wapi_settings_admin['recent']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="recent-range"><?php esc_html_e( 'Range (days)', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="recent_range" id="recent-range"
                                       value="<?php if ( $wapi_settings_admin['recent']['range'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['range'] );
								       } ?>">
                            </td>
                        </tr>


                        <tr>
                            <th><?php esc_html_e( 'Amount', 'woo-advanced-product-information' ); ?></th>
                            <td>
                                <div class="grouped fields">
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="wapi_recent" id="wapi-recent-real"
                                                   value="0" <?php checked( $wapi_settings_admin['recent']['fake'], 0 ); ?>><label
                                                    for="wapi-recent-real"><?php esc_html_e( 'Real', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="wapi_recent" id="wapi-recent-fake"
                                                   value="1" <?php checked( $wapi_settings_admin['recent']['fake'], 1 ); ?>><label
                                                    for="wapi-recent-fake"><?php esc_html_e( 'Virtual', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Virtual Value', 'woo-advanced-product-information' ); ?></th>
                            <td>
                                <div class="equal width fields">
                                    <div class="field">
                                        <label for="recent-minrand"><?php esc_html_e( 'Minrand:', 'woo-advanced-product-information' ); ?></label><input
                                                type="number" name="recent_minrand" id="recent-minrand" min="0"
                                                step="1"
                                                value="<?php if ( $wapi_settings_admin['recent']['minrand'] ) {
													echo esc_attr( $wapi_settings_admin['recent']['minrand'] );
												} ?>">
                                    </div>
                                    <div class="field">
                                        <label for="recent-maxrand"><?php esc_html_e( 'Maxrand:', 'woo-advanced-product-information' ); ?></label><input
                                                type="number" name="recent_maxrand" id="recent-maxrand" min="0"
                                                step="1"
                                                value="<?php if ( $wapi_settings_admin['recent']['maxrand'] ) {
													echo esc_attr( $wapi_settings_admin['recent']['maxrand'] );
												} ?>">

                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-text"><?php esc_html_e( 'Displayed Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="recent_text" id="recent-text"
                                       value="<?php if ( $wapi_settings_admin['recent']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['recent']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                                <p><?php esc_html_e( '{recent_quantity} - The number of orders', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '{recent_range} - The number of days to count orders', 'woo-advanced-product-information' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_position" id="recent-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['recent']['position'] ) && 'before_meta' == $wapi_settings_admin['recent']['position'] ); ?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['recent']['position'] ) && 'after_meta' == $wapi_settings_admin['recent']['position'] ); ?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_cart" <?php selected( isset( $wapi_settings_admin['recent']['position'] ) && 'before_cart' == $wapi_settings_admin['recent']['position'] ); ?>><?php esc_html_e( 'Before add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['recent']['position'] ) && 'after_cart' == $wapi_settings_admin['recent']['position'] ); ?>><?php esc_html_e( 'After add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_text_align" id="recent-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['recent']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['recent']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['recent']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['recent']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="recent-border-color"
                                       name="recent_border_color"
                                       value="<?php if ( $wapi_settings_admin['recent']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['recent']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['recent']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="recent-border-radius" min="0"
                                           name="recent_border_radius"
                                           value="<?php if ( $wapi_settings_admin['recent']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['recent']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="recent_text_color" id="recent-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['recent']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['recent']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="recent_text_bg_color" id="recent-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['recent']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['text_bg_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['recent']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['recent']['text_bg_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="recent_css"
                                          id="recent-css"><?php echo ( isset( $wapi_settings_admin['recent']['css'] ) && $wapi_settings_admin['recent']['css'] ) ? esc_textarea( $wapi_settings_admin['recent']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['recent']['include_product'] ) && is_array( $wapi_settings_admin['recent']['include_product'] ) && count( $wapi_settings_admin['recent']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['recent']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['recent']['exclude_product'] ) && is_array( $wapi_settings_admin['recent']['exclude_product'] ) && count( $wapi_settings_admin['recent']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['recent']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['recent']['include_category'] ) && is_array( $wapi_settings_admin['recent']['include_category'] ) && count( $wapi_settings_admin['recent']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['recent']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="recent-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="recent_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['recent']['exclude_category'] ) && is_array( $wapi_settings_admin['recent']['exclude_category'] ) && count( $wapi_settings_admin['recent']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['recent']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="vi-ui tab segment" data-tab="rank">
                    <table class="form-table">
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-rank"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_rank" class="wapi-checkbox-enable"
                                           id="wapi-enable-rank" <?php checked( $wapi_settings_admin['rank']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="rank-catnum"><?php esc_html_e( 'Number of categories', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="rank_catnum" id="rank-catnum"
                                       value="<?php if ( $wapi_settings_admin['rank']['catnum'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['catnum'] );
								       } ?>" min="1">
								<?php esc_html_e( 'How many categories to show if a product belong to many categories', 'woo-advanced-product-information' ); ?>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-min"><?php esc_html_e( 'Display if rank from', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="rank_min" id="rank-min"
                                       value="<?php if ( $wapi_settings_admin['rank']['min'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['min'] );
								       } ?>" min="1">
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Calculate Rank within', 'woo-advanced-product-information' ); ?></th>
                            <td>
                                <div class="grouped fields">
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="rank_by" value="week"
                                                   id="rank-week" <?php selected( $wapi_settings_admin['rank']['by'], 'week'); ?>><label
                                                    for="rank-week"><?php esc_html_e( 'Last week', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="rank_by" value="month"
                                                   id="rank-month" <?php checked( $wapi_settings_admin['rank']['by'], 'month' ); ?>><label
                                                    for="rank-month"><?php esc_html_e( 'Last month', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="vi-ui toggle checkbox">
                                            <input type="radio" name="rank_by" value="alltime"
                                                   id="rank-alltime" <?php checked( $wapi_settings_admin['rank']['by'], 'alltime' ); ?>><label
                                                    for="rank-alltime"><?php esc_html_e( 'All Time', 'woo-advanced-product-information' ); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-text"><?php esc_html_e( 'Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="rank_text" id="rank-text"
                                       value="<?php if ( $wapi_settings_admin['rank']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['rank']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><?php esc_html_e( '{rank} - Rank of the product', 'woo-advanced-product-information' ); ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><?php esc_html_e( '{category} - Category of the product if ranked', 'woo-advanced-product-information' ); ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><?php esc_html_e( '{time} - The period of time that rank is calculated', 'woo-advanced-product-information' ); ?></td>
                        </tr>
                        <tr class="wapi-settings">
                            <th>
                                <label for="rank-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td colspan="3">
                                <select name="rank_position" id="rank-position">
                                    <option value="after_title" <?php selected( isset( $wapi_settings_admin['rank']['position'] ) && 'after_title' == $wapi_settings_admin['rank']['position'] ); ?>><?php esc_html_e( 'After product title', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['rank']['position'] ) && 'after_cart' == $wapi_settings_admin['rank']['position'] ); ?>><?php esc_html_e( 'After add to cart button', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="rank_text_align" id="rank-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['rank']['text_align'], 'left' ); ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['rank']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['rank']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['rank']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="rank-border-color"
                                       name="rank_border_color"
                                       value="<?php if ( $wapi_settings_admin['rank']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['rank']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['rank']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="rank-border-radius" min="0"
                                           name="rank_border_radius"
                                           value="<?php if ( $wapi_settings_admin['rank']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['rank']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="rank_text_color" id="rank-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['rank']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['rank']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="rank_text_bg_color" id="rank-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['rank']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['text_bg_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['rank']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['rank']['text_bg_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="rank_css"
                                          id="rank-css"><?php echo ( isset( $wapi_settings_admin['rank']['css'] ) && $wapi_settings_admin['rank']['css'] ) ? esc_textarea( $wapi_settings_admin['rank']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="rank_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['rank']['include_product'] ) && is_array( $wapi_settings_admin['rank']['include_product'] ) && count( $wapi_settings_admin['rank']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['rank']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="rank_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['rank']['exclude_product'] ) && is_array( $wapi_settings_admin['rank']['exclude_product'] ) && count( $wapi_settings_admin['rank']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['rank']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="rank_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['rank']['include_category'] ) && is_array( $wapi_settings_admin['rank']['include_category'] ) && count( $wapi_settings_admin['rank']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['rank']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="rank-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="rank_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['rank']['exclude_category'] ) && is_array( $wapi_settings_admin['rank']['exclude_category'] ) && count( $wapi_settings_admin['rank']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['rank']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="vi-ui tab segment" data-tab="payment">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-payment"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_payment" class="wapi-checkbox-enable"
                                           id="wapi-enable-payment" <?php checked( $wapi_settings_admin['payment']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr class="wapi-settings">
                            <th>
                                <label for="payment-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td colspan="3">
                                <select name="payment_position" id="payment-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['payment']['position'] ) && 'before_meta' == $wapi_settings_admin['payment']['position'] ); ?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['payment']['position'] ) && 'after_meta' == $wapi_settings_admin['payment']['position'] ); ?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="payment-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td colspan="3">
                                <textarea name="payment_css"
                                          id="payment-css"><?php echo ( isset( $wapi_settings_admin['payment']['css'] ) && $wapi_settings_admin['payment']['css'] ) ? esc_textarea( $wapi_settings_admin['payment']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="form-table">
                        <tbody class="payment-methods-list">
						<?php
						$payments           = new WC_Payment_Gateways();
						$available_gateways = $payments->get_available_payment_gateways();
						if ( is_array( $available_gateways ) && sizeof( $available_gateways ) > 0 ) {
							?>
                            <tr>
                                <th><?php esc_html_e( 'Payment', 'woo-advanced-product-information' ); ?></th>
                                <th><?php esc_html_e( 'Icon([wapi_icon]/url)', 'woo-advanced-product-information' ); ?></th>
                                <th><?php esc_html_e( 'Link', 'woo-advanced-product-information' ); ?></th>
                                <th><?php esc_html_e( 'Active', 'woo-advanced-product-information' ); ?></th>
                            </tr>
							<?php
							foreach ( $available_gateways as $k => $available_gateway ) {
								$payment_id = $available_gateway->id;
								?>
                                <tr>
                                    <td><input type="hidden" class="payment-id"
                                               name="payment_id[]"
                                               value="<?php echo esc_attr( $available_gateway->id ); ?>"><?php echo esc_html( $available_gateway->title ); ?>
                                    </td>
                                    <td><input type="text" class="payment-icon" name="payment_icon[]"
                                               value="<?php if ( isset( $wapi_settings_admin['payment']['id'] ) && is_array( $wapi_settings_admin['payment']['id'] ) && count( $wapi_settings_admin['payment']['id'] ) && '' !== ( $key = array_search( $payment_id, $wapi_settings_admin['payment']['id'], true ) ) ) {
										           echo esc_attr( htmlentities( $wapi_settings_admin['payment']['icon'][ $key ] ) );
									           } ?>"><span
                                                class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                                    </td>
                                    <td><input type="text" class="payment-url" name="payment_url[]"
                                               value="<?php if ( isset( $wapi_settings_admin['payment']['id'] ) && is_array( $wapi_settings_admin['payment']['id'] ) && count( $wapi_settings_admin['payment']['id'] ) && '' !== ( $key = array_search( $payment_id, $wapi_settings_admin['payment']['id'], true ) ) ) {
										           echo esc_attr( htmlentities( $wapi_settings_admin['payment']['url'][ $key ] ) );
									           } ?>">
                                    </td>
                                    <td>
                                        <div class="vi-ui toggle checkbox">
                                            <input type="checkbox"
                                                   class="payment-active-check" <?php checked( isset( $wapi_settings_admin['payment']['id'] ) && is_array( $wapi_settings_admin['payment']['id'] ) && count( $wapi_settings_admin['payment']['id'] ) && '' !== ( $key = array_search( $payment_id, $wapi_settings_admin['payment']['id'], true ) ) && $wapi_settings_admin['payment']['active'][ $key ] ); ?>><label></label>
                                            <input type="hidden" class="payment-active" name="payment_active[]"
                                                   value="<?php if ( isset( $wapi_settings_admin['payment']['id'] ) && is_array( $wapi_settings_admin['payment']['id'] ) && count( $wapi_settings_admin['payment']['id'] ) && '' !== ( $key = array_search( $payment_id, $wapi_settings_admin['payment']['id'], true ) ) ) {
												       echo esc_attr( $wapi_settings_admin['payment']['active'][ $key ] );
											       } ?>">
                                        </div>
                                    </td>

                                </tr>
								<?php
							}
						}
						?>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui tab segment" data-tab="social_proof">
                    <table class="form-table">
                        <tbody>
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-social-proof"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_social_proof" class="wapi-checkbox-enable"
                                           id="wapi-enable-social-proof" <?php checked( $wapi_settings_admin['social_proof']['enable'], 'on' );?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="social-proof-text"><?php esc_html_e( 'Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="social_proof_text" id="social-proof-text"
                                       value=" <?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['social_proof']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><?php esc_html_e( '{social_proof_views} - Number of views of the product', 'woo-advanced-product-information' ); ?></td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="social_proof_position" id="social-proof-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['social_proof']['position'] ) && 'before_meta' == $wapi_settings_admin['social_proof']['position'] );?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['social_proof']['position'] ) && 'after_meta' == $wapi_settings_admin['social_proof']['position'] );?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-fake"><?php esc_html_e( 'Virtual Views To Plus Real Views', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input
                                        type="number" name="social_proof_fake" id="social-proof-fake" min="0"
                                        step="1"
                                        value="<?php if ( $wapi_settings_admin['social_proof']['fake'] ) {
											echo esc_attr( $wapi_settings_admin['social_proof']['fake'] );
										} ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="social_proof_text_align" id="social-proof-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['social_proof'] && 'left' == $wapi_settings_admin['social_proof']['text_align'] );?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['social_proof'] && 'center' == $wapi_settings_admin['social_proof']['text_align'] );?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['social_proof'] && 'right' == $wapi_settings_admin['social_proof']['text_align'] );?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['social_proof'] && 'inherit' == $wapi_settings_admin['social_proof']['text_align'] ) ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="social-proof-border-color"
                                       name="social_proof_border_color"
                                       value="<?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( $wapi_settings_admin['social_proof']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['social_proof'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['social_proof']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="social-proof-border-radius" min="0"
                                           name="social_proof_border_radius"
                                           value="<?php if ( $wapi_settings_admin['social_proof']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['social_proof']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="social_proof_text_color" id="social-proof-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( $wapi_settings_admin['social_proof']['text_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( $wapi_settings_admin['social_proof']['text_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="social_proof_text_bg_color" id="social-proof-text-bg-color"
                                       type="text" class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( $wapi_settings_admin['social_proof']['text_bg_color'] );
								       } ?>"
                                       style="background-color:<?php if ( $wapi_settings_admin['social_proof'] ) {
									       echo esc_attr( $wapi_settings_admin['social_proof']['text_bg_color'] );
								       } ?> ;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="social-proof-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="social_proof_css"
                                          id="social-proof-css"><?php echo ( isset( $wapi_settings_admin['social_proof']['css'] ) && $wapi_settings_admin['social_proof']['css'] ) ? esc_textarea( $wapi_settings_admin['social_proof']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
                <div class="vi-ui   tab segment" data-tab="coupon">
                    <table class="form-table">
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-coupon"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_coupon" class="wapi-checkbox-enable"
                                           id="wapi-enable-coupon" <?php checked( $wapi_settings_admin['coupon']['enable'], 'on' );?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="coupon-code"><?php esc_html_e( 'Coupon Code', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select id="coupon-code" name="coupon_code"
                                        class="coupon-search select2-selection--single"
                                        data-placeholder="<?php esc_html_e( 'Please fill in your coupon code', 'woo-advanced-product-information' ) ?>">
									<?php
									if ( "" != $wapi_settings_admin['coupon']['code'] ) {
										echo '<option value="' . esc_attr( $wapi_settings_admin['coupon']['code'] ) . '" selected>' . esc_html( get_post( $wapi_settings_admin['coupon']['code'] )->post_title ) . '</option>';
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="coupon-text"><?php esc_html_e( 'Coupon Massage', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="coupon_text" id="coupon-text"
                                       value=" <?php if ( $wapi_settings_admin['coupon']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['coupon']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                                <p><?php esc_html_e( '{coupon_code} - The code of the Coupon', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '{coupon_amount} - The amount of the Coupon', 'woo-advanced-product-information' ); ?></p>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="coupon-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="coupon_position" id="coupon-position">
                                    <option value="before_cart" <?php selected( isset( $wapi_settings_admin['coupon']['position'] ) && 'before_cart' == $wapi_settings_admin['coupon']['position'] );?>><?php esc_html_e( 'Before add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['coupon']['position'] ) && 'after_cart' == $wapi_settings_admin['coupon']['position'] );?>><?php esc_html_e( 'After add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_price" <?php selected( isset( $wapi_settings_admin['coupon']['position'] ) && 'before_price' == $wapi_settings_admin['coupon']['position'] );?>><?php esc_html_e( 'Before price', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_price" <?php selected( isset( $wapi_settings_admin['coupon']['position'] ) && 'after_price' == $wapi_settings_admin['coupon']['position'] );?>><?php esc_html_e( 'After price', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="coupon-border-color"><?php esc_html_e( 'Border color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="coupon_border_color" id="coupon-border-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( isset( $wapi_settings_admin['coupon']['border_color'] ) && $wapi_settings_admin['coupon']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['border_color'] );
								       } ?>"
                                       style="background-color: <?php if ( isset( $wapi_settings_admin['coupon']['border_color'] ) && $wapi_settings_admin['coupon']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['border_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="coupon-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="coupon-border-radius" min="0"
                                           name="coupon_border_radius"
                                           value="<?php if ( $wapi_settings_admin['coupon']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['coupon']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="coupon-text-color"><?php esc_html_e( 'Message Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="coupon_text_color" id="coupon-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['coupon']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['coupon']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="coupon-text-bg-color"><?php esc_html_e( 'Message Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="coupon_text_bg_color" id="coupon-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['coupon']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['text_bg_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['coupon']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['coupon']['text_bg_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="coupon-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="coupon_css"
                                          id="coupon-css"><?php echo ( isset( $wapi_settings_admin['coupon']['css'] ) && $wapi_settings_admin['coupon']['css'] ) ? esc_textarea( $wapi_settings_admin['coupon']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                    </table>
                </div>
                <div class="vi-ui   tab segment" data-tab="custom">
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th>
                                <label for="wapi-enable-custom-table"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_custom_table" class="wapi-checkbox-enable"
                                           id="wapi-enable-custom-table" <?php checked( $wapi_settings_admin['custom']['enable'], 'on' );?>><label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="custom-table-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="custom_table_position" id="custom-table-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['custom']['position'] ) && 'before_meta' == $wapi_settings_admin['custom']['position'] );?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['custom']['position'] ) && 'after_meta' == $wapi_settings_admin['custom']['position'] );?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_cart" <?php selected( isset( $wapi_settings_admin['custom']['position'] ) && 'before_cart' == $wapi_settings_admin['custom']['position'] ); ?>><?php esc_html_e( 'Before add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['custom']['position'] ) && 'after_cart' == $wapi_settings_admin['custom']['position'] ); ?>><?php esc_html_e( 'After add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_footer" <?php selected( isset( $wapi_settings_admin['custom']['position'] ) && 'before_footer' == $wapi_settings_admin['custom']['position'] ); ?>><?php esc_html_e( 'Before footer', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-table-layout"><?php esc_html_e( 'Layout', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="custom_table_layout" id="custom-table-layout">
                                    <option value="1" <?php selected( isset( $wapi_settings_admin['custom']['layout'] ) && '1' == $wapi_settings_admin['custom']['layout'] ); ?>><?php esc_html_e( 'Multiple columns', 'woo-advanced-product-information' ); ?></option>
                                    <option value="2" <?php selected( isset( $wapi_settings_admin['custom']['layout'] ) && '2' == $wapi_settings_admin['custom']['layout'] ) ?>><?php esc_html_e( 'Multiple rows', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-table-font-size"><?php esc_html_e( 'Font size', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="custom-table-font-size" name="custom_table_font_size"
                                           min="0"
                                           value="<?php if ( isset( $wapi_settings_admin['custom']['font_size'] ) && $wapi_settings_admin['custom']['font_size'] ) {
										       echo esc_attr( $wapi_settings_admin['custom']['font_size'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-table-background"><?php esc_html_e( 'Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input id="custom-table-background" name="custom_table_background"
                                       type="text" class="color-picker"
                                       value="<?php if ( isset( $wapi_settings_admin['custom']['background'] ) && $wapi_settings_admin['custom']['background'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['background'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['custom']['background'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['background'] );
								       } ?>;"/>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="custom-table-width-icon"><?php esc_html_e( 'Icons Size', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="custom-table-width-icon" name="custom_table_width_icon"
                                           min="0"
                                           value="<?php if ( isset( $wapi_settings_admin['custom']['icon_width'] ) && $wapi_settings_admin['custom']['icon_width'] ) {
										       echo esc_attr( $wapi_settings_admin['custom']['icon_width'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-table-color-icon"><?php esc_html_e( 'Icons Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="custom_table_color_icon" id="custom-table-color-icon" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['custom']['icon_color'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['icon_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['custom']['icon_color'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['icon_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-table-color-text"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="custom_table_color_text" id="custom-table-color-text" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['custom']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['custom']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['custom']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="custom-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="custom_css"
                                          id="custom-css"><?php echo ( isset( $wapi_settings_admin['custom']['css'] ) && $wapi_settings_admin['custom']['css'] ) ? esc_textarea( $wapi_settings_admin['custom']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="form-table">
                        <tbody>
                        <tr style="border-top: 1px solid rgba(34,60,68,0.29);">
                            <th colspan="5"><?php esc_html_e( 'Add/Remove items:', 'woo-advanced-product-information' ); ?></th>
                        </tr>
                        <tr class="row-edit">
                            <th><?php esc_html_e( 'Icon', 'woo-advanced-product-information' ); ?></th>
                            <th><?php esc_html_e( 'Heading', 'woo-advanced-product-information' ); ?></th>
                            <th><?php esc_html_e( 'Content', 'woo-advanced-product-information' ); ?></th>
                            <th><?php esc_html_e( 'Url', 'woo-advanced-product-information' ); ?></th>
                            <th></th>
                        </tr>
                        </tbody>
                    </table>
                    <table class="form-table">
                        <tbody class="ui-sortable" id="custom-table">
						<?php
						if ( $wapi_settings_admin['custom']['row'] ) {
							$rows = $wapi_settings_admin['custom']['row'];
							if ( isset( $rows['icon'][0] ) ) {
								for ( $i = 0; $i < sizeof( $rows['icon'] ); $i ++ ) {
									?>
                                    <tr class="row-edit">
                                        <td>
                                            <input style="width:150px;" class="custom-row-icon" type="hidden"
                                                   name="custom_row_icon[]"
                                                   value="<?php echo esc_attr( $rows['icon'][ $i ] ); ?>"/>
                                            <span
                                                    class="<?php echo esc_attr( wapi_get_icon( $rows['icon'][ $i ] ) ); ?>"
                                                    style=" vertical-align: middle;<?php if ( $wapi_settings_admin['custom']['icon_color'] ) {
														echo esc_attr( 'color: ' . $wapi_settings_admin['custom']['icon_color'] . ';' );
													}
													?>"></span><a href="javascript:void(0);"
                                                                  class="vi-ui button choose-icon"><?php esc_html_e( 'Choose an icon', 'woo-advanced-product-information' ); ?></a>
                                        </td>
                                        <td>
                                            <input type="text" name="custom_row_heading[]" class="custom-row-heading"
                                                   value="<?php echo esc_attr( htmlentities( $rows['heading'][ $i ] ) ); ?>"
                                                   style="<?php if ( $wapi_settings_admin['custom']['text_color'] ) {
												       echo esc_attr( 'color: ' . $wapi_settings_admin['custom']['text_color'] . ';' );
											       } ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="custom_row_text[]" class="custom-row-text"
                                                   value="<?php echo esc_attr( htmlentities( $rows['text'][ $i ] ) ); ?>"
                                                   style="<?php if ( $wapi_settings_admin['custom']['text_color'] ) {
												       echo esc_attr( 'color: ' . $wapi_settings_admin['custom']['text_color'] . ';' );
											       } ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="custom_row_url[]" class="custom-row-url"
                                                   value="<?php echo esc_attr( htmlentities( $rows['url'][ $i ] ) ); ?>"
                                            />
                                        </td>

                                        <td>
                                            <a href="javascript:void(0);"
                                               class="vi-ui negative button delete-row"><?php esc_html_e( 'Delete', 'woo-advanced-product-information' ); ?></a>
                                        </td>
                                    </tr>
									<?php
								}
							}
						}
						?>
                        </tbody>
                    </table>
                    <a id="add-row" class="vi-ui positive button"
                       href="javascript:void(0);"><?php esc_html_e( 'Add row', 'woo-advanced-product-information' ); ?></a>

                </div>
                <div class="vi-ui   tab segment" data-tab="orders">
                    <table class="form-table">
                        <tr class="wapi-settings">
                            <th>
                                <label for="wapi-enable-orders"><?php esc_html_e( 'Enable', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="vi-ui toggle checkbox">
                                    <input type="checkbox" name="wapi_enable_orders" class="wapi-checkbox-enable"
                                           id="wapi-enable-orders" <?php checked( $wapi_settings_admin['orders']['enable'], 'on' ); ?>>
                                    <label></label>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="orders-quantity"><?php esc_html_e( 'Quantity', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="number" name="orders_quantity" id="orders-quantity"
                                       value="<?php if ( $wapi_settings_admin['orders']['quantity'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['quantity'] );
								       } ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-text"><?php esc_html_e( 'Displayed Text', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" name="orders_text" id="orders-text"
                                       value="<?php if ( $wapi_settings_admin['orders']['text'] ) {
									       echo esc_attr( htmlentities( $wapi_settings_admin['orders']['text'] ) );
								       } ?>"><span
                                        class="wapi-select-icon-button"><?php esc_html_e( 'Add Icons', 'woo-advanced-product-information' ); ?></span>
                                <p><?php esc_html_e( '{customers_list} - The list of customers\' first names', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '(eg:Hyro, Alex, Peter )', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '{customers_list_with_countries} - The list of customers\' first names with their countries', 'woo-advanced-product-information' ); ?></p>
                                <p><?php esc_html_e( '(eg:Hyro from Vietnam, Alex from United state, Peter from United Kingdom )', 'woo-advanced-product-information' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-position"><?php esc_html_e( 'Position', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_position" id="orders-position">
                                    <option value="before_meta" <?php selected( isset( $wapi_settings_admin['orders']['position'] ) && 'before_meta' == $wapi_settings_admin['orders']['position'] ); ?>><?php esc_html_e( 'Before product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_meta" <?php selected( isset( $wapi_settings_admin['orders']['position'] ) && 'after_meta' == $wapi_settings_admin['orders']['position'] ); ?>><?php esc_html_e( 'After product meta', 'woo-advanced-product-information' ); ?></option>
                                    <option value="before_cart" <?php selected( isset( $wapi_settings_admin['orders']['position'] ) && 'before_cart' == $wapi_settings_admin['orders']['position'] ); ?>><?php esc_html_e( 'Before add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                    <option value="after_cart" <?php selected( isset( $wapi_settings_admin['orders']['position'] ) && 'after_cart' == $wapi_settings_admin['orders']['position'] ); ?>><?php esc_html_e( 'After add-to-cart', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>

                        <tr>
                            <th>
                                <label for="orders-text-align"><?php esc_html_e( 'Text Align', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_text_align" id="orders-text-align">
                                    <option value="left" <?php selected( $wapi_settings_admin['orders']['text_align'], 'left' ) ?>><?php esc_html_e( 'Left', 'woo-advanced-product-information' ); ?></option>
                                    <option value="center" <?php selected( $wapi_settings_admin['orders']['text_align'], 'center' ); ?>><?php esc_html_e( 'Center', 'woo-advanced-product-information' ); ?></option>
                                    <option value="right" <?php selected( $wapi_settings_admin['orders']['text_align'], 'right' ); ?>><?php esc_html_e( 'Right', 'woo-advanced-product-information' ); ?></option>
                                    <option value="inherit" <?php selected( $wapi_settings_admin['orders']['text_align'], 'inherit' ); ?>><?php esc_html_e( 'Inherit', 'woo-advanced-product-information' ); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-border-color"><?php esc_html_e( 'Border Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input type="text" class="color-picker" id="orders-border-color"
                                       name="orders_border_color"
                                       value="<?php if ( $wapi_settings_admin['orders']['border_color'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['border_color'] );
								       } ?>" style="<?php if ( $wapi_settings_admin['orders']['border_color'] ) {
									echo esc_attr( 'background-color:' . $wapi_settings_admin['orders']['border_color'] );
								} ?>;">
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-border-radius"><?php esc_html_e( 'Border radius', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <div class="inline fields">
                                    <input type="number" id="orders-border-radius" min="0"
                                           name="orders_border_radius"
                                           value="<?php if ( $wapi_settings_admin['orders']['border_radius'] ) {
										       echo esc_attr( $wapi_settings_admin['orders']['border_radius'] );
									       } ?>"><?php esc_html_e( 'px', 'woo-advanced-product-information' ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-text-color"><?php esc_html_e( 'Text Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="orders_text_color" id="orders-text-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['orders']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['text_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['orders']['text_color'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['text_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-text-bg-color"><?php esc_html_e( 'Text Background Color', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <input name="orders_text_bg_color" id="orders-text-bg-color" type="text"
                                       class="color-picker"
                                       value="<?php if ( $wapi_settings_admin['orders']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['text_bg_color'] );
								       } ?>"
                                       style="background-color: <?php if ( $wapi_settings_admin['orders']['text_bg_color'] ) {
									       echo esc_attr( $wapi_settings_admin['orders']['text_bg_color'] );
								       } ?>;"/>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-css"><?php esc_html_e( 'Custom css', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <textarea name="orders_css"
                                          id="orders-css"><?php echo ( isset( $wapi_settings_admin['orders']['css'] ) && $wapi_settings_admin['orders']['css'] ) ? esc_textarea( $wapi_settings_admin['orders']['css'] ) : '' ?></textarea>

                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-include-product"><?php esc_html_e( 'Include products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_include_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['orders']['include_product'] ) && is_array( $wapi_settings_admin['orders']['include_product'] ) && count( $wapi_settings_admin['orders']['include_product'] ) ) {
										foreach ( $wapi_settings_admin['orders']['include_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-exclude-product"><?php esc_html_e( 'Exclude products', 'woo-advanced-product-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_exclude_product[]" class="search-product" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['orders']['exclude_product'] ) && is_array( $wapi_settings_admin['orders']['exclude_product'] ) && count( $wapi_settings_admin['orders']['exclude_product'] ) ) {
										foreach ( $wapi_settings_admin['orders']['exclude_product'] as $product_id ) {
											$product = wc_get_product( $product_id );
											?>
                                            <option value="<?php echo esc_attr( $product_id ) ?>"
                                                    selected><?php echo esc_html( $product->get_title() ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-include-category"><?php esc_html_e( 'Include categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_include_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['orders']['include_category'] ) && is_array( $wapi_settings_admin['orders']['include_category'] ) && count( $wapi_settings_admin['orders']['include_category'] ) ) {
										foreach ( $wapi_settings_admin['orders']['include_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="orders-exclude-category"><?php esc_html_e( 'Exclude categories', 'woocommerce-advanced-category-information' ); ?></label>
                            </th>
                            <td>
                                <select name="orders_exclude_category[]" class="search-category" multiple="multiple">
									<?php
									if ( isset( $wapi_settings_admin['orders']['exclude_category'] ) && is_array( $wapi_settings_admin['orders']['exclude_category'] ) && count( $wapi_settings_admin['orders']['exclude_category'] ) ) {
										foreach ( $wapi_settings_admin['orders']['exclude_category'] as $category_id ) {
											$category = get_term( $category_id );
											?>
                                            <option value="<?php echo esc_attr( $category_id ) ?>"
                                                    selected><?php echo esc_html( $category->name ); ?></option>
											<?php
										}
									}
									?>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
                <p>
                    <input type="submit" class="vi-ui button primary" name="submit"
                           value="<?php esc_html_e( 'Save', 'woo-advanced-product-information' ); ?>">
					<?php
					?>
                    <input type="submit" class="vi-ui negative button" id="wapi_delete_option" name="wapi_delete_option"
                           value="<?php esc_html_e( 'Reset Option', 'woo-advanced-product-information' ); ?>">
                </p>

            </form>
            <div class="wapi-icons-wrap">
				<?php wapi_select_icon(); ?>
            </div>
        </div>
        <div class="wapi-overlay"></div>

		<?php
        // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		do_action( 'villatheme_support_woo-advanced-product-information' );
	}

	public function search_cate() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );

		if ( empty( $keyword ) ) {
			die();
		}
		$categories = get_terms(
			array(
				'taxonomy' => 'product_cat',
				'orderby'  => 'name',
				'order'    => 'ASC',
				'search'   => $keyword,
				'number'   => 100
			)
		);
		$items      = array();
		if ( count( $categories ) ) {
			foreach ( $categories as $category ) {
				$item    = array(
					'id'   => $category->term_id,
					'text' => $category->name
				);
				$items[] = $item;
			}
		}
		wp_send_json( $items );
		die;
	}

	public function search_product() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		ob_start();

		$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );

		if ( empty( $keyword ) ) {
			die();
		}
		$arg            = array(
			'post_status'    => 'publish',
			'post_type'      => 'product',
			'posts_per_page' => 50,
			's'              => $keyword

		);
		$the_query      = new WP_Query( $arg );
		$found_products = array();
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$prd = wc_get_product( get_the_ID() );

				if ( $prd->has_child() && $prd->is_type( 'variable' ) ) {
					$product_children = $prd->get_children();
					if ( count( $product_children ) ) {
						foreach ( $product_children as $product_child ) {
							if ( woocommerce_version_check() ) {
								$product = array(
									'id'   => $product_child,
									'text' => get_the_title( $product_child )
								);

							} else {
								$child_wc  = wc_get_product( $product_child );
								$get_atts  = $child_wc->get_variation_attributes();
								$attr_name = array_values( $get_atts )[0];
								$product   = array(
									'id'   => $product_child,
									'text' => get_the_title() . ' - ' . $attr_name
								);

							}
							$found_products[] = $product;
						}
					}
				} else {
					$product_id    = get_the_ID();
					$product_title = get_the_title();
					$the_product   = new WC_Product( $product_id );
					if ( ! $the_product->is_in_stock() ) {
						$product_title .= ' (out-of-stock)';
					}
					$product          = array(
                            'id'   => $product_id,
                            'text' => $product_title
                    );
					$found_products[] = $product;
				}
			}
		}
		wp_send_json( $found_products );
		die;
	}

	public function save_settings() {
		$wapi_nonce_field = isset( $_POST['wapi_nonce_field'] ) ? sanitize_key( wp_unslash( $_POST['wapi_nonce_field'] ) ) : '';
		if ( ! wp_verify_nonce( $wapi_nonce_field, 'wapi_settings_page_save' ) ) {
			return;
		}
		$wapi_settings_admin_args = array(
			'review'       => array(
				'enable'           => isset( $_POST['wapi_enable_review'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_review'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_review'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_review'] ) ) : 'off',
				'no_review'        => isset( $_POST['no_review'] ) ? sanitize_text_field( wp_unslash( $_POST['no_review'] ) ) : "",
				'satisfied'        => isset( $_POST['satisfied_reviews'] ) ? sanitize_text_field( wp_unslash( $_POST['satisfied_reviews'] ) ) : "",
				'min_rate'         => isset( $_POST['satisfied_min'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['satisfied_min'] ) ) ) : "",
				'text_align'       => isset( $_POST['review_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['review_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['review_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['review_border_color'] ) ) : "",
				'border_radius'    => isset( $_POST['review_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['review_border_radius'] ) ) : "",
				'text_color'       => isset( $_POST['review_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['review_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['review_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['review_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['review_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['review_css'] ) ) : "",
				'include_product'  => isset( $_POST['review_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['review_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['review_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['review_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['review_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['review_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['review_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['review_include_category'] ) ) : array(),
			),
			'instock'      => array(
				'enable'           => isset( $_POST['wapi_enable_instock'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_instock'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_instock'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_instock'] ) ) : 'off',
				'text'             => isset( $_POST['instock_text'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_text'] ) ) : "",
				'fake'             => isset( $_POST['wapi_instock'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['wapi_instock'] ) ) ) : "",
				'minrand'          => isset( $_POST['instock_minrand'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['instock_minrand'] ) ) ) : "",
				'maxrand'          => isset( $_POST['instock_maxrand'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['instock_maxrand'] ) ) ) : "",
				'position'         => isset( $_POST['instock_position'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_position'] ) ) : "",
				'style'            => isset( $_POST['instock_style'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_style'] ) ) : "",
				'text_align'       => isset( $_POST['instock_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['instock_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_border_color'] ) ) : "",
				'border_radius'    => isset( $_POST['instock_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_border_radius'] ) ) : "",
				'text_color'       => isset( $_POST['instock_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['instock_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_text_bg_color'] ) ) : "",
				'bar_color1'       => isset( $_POST['instock_bar_color_1'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_bar_color_1'] ) ) : "",
				'bar_color2'       => isset( $_POST['instock_bar_color_2'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_bar_color_2'] ) ) : "",
				'width_min'        => isset( $_POST['instock_bar_width_min'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_bar_width_min'] ) ) : "",
				'width_max'        => isset( $_POST['instock_bar_width_max'] ) ? sanitize_text_field( wp_unslash( $_POST['instock_bar_width_max'] ) ) : "",
				'css'              => isset( $_POST['instock_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['instock_css'] ) ) : "",
				'include_product'  => isset( $_POST['instock_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['instock_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['instock_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['instock_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['instock_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['instock_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['instock_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['instock_include_category'] ) ) : array(),
			),
			'shipping'     => array(
				'enable' => isset( $_POST['wapi_enable_shipping'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_shipping'] ) ) : 'off',
				'mobile' => isset( $_POST['wapi_mobile_shipping'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_shipping'] ) ) : 'off',
				'css'    => isset( $_POST['shipping_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['shipping_css'] ) ) : "",
			),
			'sale'         => array(
				'enable'           => isset( $_POST['wapi_enable_sale'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_sale'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_sale'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_sale'] ) ) : 'off',
				'text'             => isset( $_POST['sale_text'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_text'] ) ) : "",
				'position'         => isset( $_POST['sale_position'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_position'] ) ) : "",
				'text_align'       => isset( $_POST['sale_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['sale_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_border_color'] ) ) : "",
				'border_radius'    => isset( $_POST['sale_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_border_radius'] ) ) : "",
				'text_color'       => isset( $_POST['sale_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['sale_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['sale_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['sale_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sale_css'] ) ) : "",
				'include_product'  => isset( $_POST['sale_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['sale_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['sale_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['sale_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['sale_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['sale_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['sale_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['sale_include_category'] ) ) : array(),
			),
			'countdown'    => array(
				'enable'           => isset( $_POST['wapi_enable_countdown'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_countdown'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_countdown'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_countdown'] ) ) : 'off',
				'fake'             => isset( $_POST['wapi_countdown'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['wapi_countdown'] ) ) ) : "",
				'start'            => isset( $_POST['countdown_start'] ) ? ( strtotime( sanitize_text_field( wp_unslash( $_POST['countdown_start'] ) ) ) - 3600 * get_option( 'gmt_offset' ) ) : "",
				'end'              => isset( $_POST['countdown_end'] ) ? ( strtotime( sanitize_text_field( wp_unslash( $_POST['countdown_end'] ) ) ) - 3600 * get_option( 'gmt_offset' ) ) : "",
				'text'             => isset( $_POST['countdown_text'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_text'] ) ) : "",
				'style'            => isset( $_POST['countdown_style'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_style'] ) ) : "",
				'type'             => isset( $_POST['countdown_type'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_type'] ) ) : "",
				'position'         => isset( $_POST['countdown_position'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_position'] ) ) : "",
				'text_align'       => isset( $_POST['countdown_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['countdown_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_border_color'] ) ) : "",
				'border_radius'    => isset( $_POST['countdown_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_border_radius'] ) ) : "",
				'text_color'       => isset( $_POST['countdown_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['countdown_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['countdown_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['countdown_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['countdown_css'] ) ) : "",
				'include_product'  => isset( $_POST['countdown_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['countdown_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['countdown_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['countdown_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['countdown_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['countdown_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['countdown_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['countdown_include_category'] ) ) : array(),
			),
			'recent'       => array(
				'enable'           => isset( $_POST['wapi_enable_recent_order'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_recent_order'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_recent'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_recent'] ) ) : 'off',
				'range'            => isset( $_POST['recent_range'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['recent_range'] ) ) ) : 10,
				'fake'             => isset( $_POST['wapi_recent'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['wapi_recent'] ) ) ) : "{recent_quantity} orders in the last {recent_range} days.",
				'minrand'          => isset( $_POST['recent_minrand'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['recent_minrand'] ) ) ) : "",
				'maxrand'          => isset( $_POST['recent_maxrand'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['recent_maxrand'] ) ) ) : "",
				'text'             => isset( $_POST['recent_text'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_text'] ) ) : 10,
				'position'         => isset( $_POST['recent_position'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_position'] ) ) : '',
				'text_align'       => isset( $_POST['recent_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['recent_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_border_color'] ) ) : "",
				'border_radius'    => isset( $_POST['recent_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_border_radius'] ) ) : "",
				'text_color'       => isset( $_POST['recent_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['recent_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['recent_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['recent_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['recent_css'] ) ) : "",
				'include_product'  => isset( $_POST['recent_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['recent_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['recent_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['recent_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['recent_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['recent_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['recent_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['recent_include_category'] ) ) : array(),
			),
			'rank'         => array(
				'enable'           => isset( $_POST['wapi_enable_rank'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_rank'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_rank'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_rank'] ) ) : 'off',
				'position'         => isset( $_POST['rank_position'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_position'] ) ) : '',
				'catnum'           => isset( $_POST['rank_catnum'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['rank_catnum'] ) ) ) : 2,
				'min'              => isset( $_POST['rank_min'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['rank_min'] ) ) ) : 1,
				'by'               => isset( $_POST['rank_by'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_by'] ) ) : "",
				'text'             => isset( $_POST['rank_text'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_text'] ) ) : "",
				'text_align'       => isset( $_POST['rank_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['rank_border_color'] ) ? ( sanitize_text_field( wp_unslash( $_POST['rank_border_color'] ) ) ) : "",
				'border_radius'    => isset( $_POST['rank_border_radius'] ) ? ( sanitize_text_field( wp_unslash( $_POST['rank_border_radius'] ) ) ) : "",
				'text_color'       => isset( $_POST['rank_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['rank_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['rank_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['rank_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['rank_css'] ) ) : "",
				'include_product'  => isset( $_POST['rank_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rank_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['rank_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rank_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['rank_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rank_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['rank_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['rank_include_category'] ) ) : array(),
			),
			'payment'      => array(
				'enable'   => isset( $_POST['wapi_enable_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_payment'] ) ) : 'off',
				'mobile'   => isset( $_POST['wapi_mobile_payment'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_payment'] ) ) : 'off',
				'position' => isset( $_POST['payment_position'] ) ? sanitize_text_field( wp_unslash( $_POST['payment_position'] ) ) : array(),
				'id'       => isset( $_POST['payment_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['payment_id'] ) ) : array(),
				'icon'     => isset( $_POST['payment_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['payment_icon'] ) ) : array(),
				'url'      => isset( $_POST['payment_url'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['payment_url'] ) ) : array(),
				'active'   => isset( $_POST['payment_active'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['payment_active'] ) ) : array(),
				'css'      => isset( $_POST['payment_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['payment_css'] ) ) : "",
			),
			'social_proof' => array(
				'enable'        => isset( $_POST['wapi_enable_social_proof'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_social_proof'] ) ) : 'off',
				'mobile'        => isset( $_POST['wapi_mobile_social_proof'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_social_proof'] ) ) : 'off',

				'text'          => isset( $_POST['social_proof_text'] ) ? sanitize_text_field( wp_unslash( $_POST['social_proof_text'] ) ) : "",
				'position'      => isset( $_POST['social_proof_position'] ) ? sanitize_text_field( wp_unslash( $_POST['social_proof_position'] ) ) : "",
				'fake'          => isset( $_POST['social_proof_fake'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['social_proof_fake'] ) ) ) : "",
				'text_align'    => isset( $_POST['social_proof_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['social_proof_text_align'] ) ) : "",
				'border_color'  => isset( $_POST['social_proof_border_color'] ) ? ( sanitize_text_field( wp_unslash( $_POST['social_proof_border_color'] ) ) ) : "",
				'border_radius' => isset( $_POST['social_proof_border_radius'] ) ? ( sanitize_text_field( wp_unslash( $_POST['social_proof_border_radius'] ) ) ) : "",
				'text_color'    => isset( $_POST['social_proof_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['social_proof_text_color'] ) ) : "",
				'text_bg_color' => isset( $_POST['social_proof_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['social_proof_text_bg_color'] ) ) : "",
				'css'           => isset( $_POST['social_proof_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['social_proof_css'] ) ) : "",
			),
			'coupon'       => array(
				'enable'        => isset( $_POST['wapi_enable_coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_coupon'] ) ) : 'off',
				'mobile'        => isset( $_POST['wapi_mobile_coupon'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_coupon'] ) ) : 'off',
				'code'          => isset( $_POST['coupon_code'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['coupon_code'] ) ) ) : "",
				'text'          => isset( $_POST['coupon_text'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_text'] ) ) : "",
				'position'      => isset( $_POST['coupon_position'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_position'] ) ) : "",
				'border_color'  => isset( $_POST['coupon_border_color'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_border_color'] ) ) : "",
				'border_radius' => isset( $_POST['coupon_border_radius'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_border_radius'] ) ) : "",
				'text_color'    => isset( $_POST['coupon_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_text_color'] ) ) : "",
				'text_bg_color' => isset( $_POST['coupon_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['coupon_text_bg_color'] ) ) : "",
				'css'           => isset( $_POST['coupon_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['coupon_css'] ) ) : "",
			),
			'custom'       => array(
				'enable'     => isset( $_POST['wapi_enable_custom_table'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_custom_table'] ) ) : 'off',
				'mobile'     => isset( $_POST['wapi_mobile_custom'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_custom'] ) ) : 'off',
				'layout'     => isset( $_POST['custom_table_layout'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_table_layout'] ) ) : "",
				'position'   => isset( $_POST['custom_table_position'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_table_position'] ) ) : "",
				'background' => isset( $_POST['custom_table_background'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_table_background'] ) ) : "",
				'font_size'  => isset( $_POST['custom_table_font_size'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['custom_table_font_size'] ) ) ) : 0,
				'icon_width' => isset( $_POST['custom_table_width_icon'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['custom_table_width_icon'] ) ) ) : 0,
				'icon_color' => isset( $_POST['custom_table_color_icon'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_table_color_icon'] ) ) : "",
				'text_color' => isset( $_POST['custom_table_color_text'] ) ? sanitize_text_field( wp_unslash( $_POST['custom_table_color_text'] ) ) : "",
				'css'        => isset( $_POST['custom_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['custom_css'] ) ) : "",
				'row'        => array(
					'icon'    => isset( $_POST['custom_row_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['custom_row_icon'] ) ) : array(),
					'heading' => isset( $_POST['custom_row_heading'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['custom_row_heading'] ) ) : array(),
					'text'    => isset( $_POST['custom_row_text'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['custom_row_text'] ) ) : array(),
					'url'     => isset( $_POST['custom_row_url'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['custom_row_url'] ) ) : array(),
				)
			),
			'orders'       => array(
				'enable'           => isset( $_POST['wapi_enable_orders'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_enable_orders'] ) ) : 'off',
				'mobile'           => isset( $_POST['wapi_mobile_orders'] ) ? sanitize_text_field( wp_unslash( $_POST['wapi_mobile_orders'] ) ) : 'off',
				'quantity'         => isset( $_POST['orders_quantity'] ) ? absint( sanitize_text_field( wp_unslash( $_POST['orders_quantity'] ) ) ) : 10,
				'text'             => isset( $_POST['orders_text'] ) ? sanitize_text_field( wp_unslash( $_POST['orders_text'] ) ) : 10,
				'position'         => isset( $_POST['orders_position'] ) ? sanitize_text_field( wp_unslash( $_POST['orders_position'] ) ) : '',
				'text_align'       => isset( $_POST['orders_text_align'] ) ? sanitize_text_field( wp_unslash( $_POST['orders_text_align'] ) ) : "",
				'border_color'     => isset( $_POST['orders_border_color'] ) ? ( sanitize_text_field( wp_unslash( $_POST['orders_border_color'] ) ) ) : "",
				'border_radius'    => isset( $_POST['orders_border_radius'] ) ? ( sanitize_text_field( wp_unslash( $_POST['orders_border_radius'] ) ) ) : "",
				'text_color'       => isset( $_POST['orders_text_color'] ) ? sanitize_text_field( wp_unslash( $_POST['orders_text_color'] ) ) : "",
				'text_bg_color'    => isset( $_POST['orders_text_bg_color'] ) ? sanitize_text_field( wp_unslash( $_POST['orders_text_bg_color'] ) ) : "",
				'css'              => isset( $_POST['orders_css'] ) ? sanitize_textarea_field( wp_unslash( $_POST['orders_css'] ) ) : "",
				'include_product'  => isset( $_POST['orders_include_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['orders_include_product'] ) ) : array(),
				'exclude_product'  => isset( $_POST['orders_exclude_product'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['orders_exclude_product'] ) ) : array(),
				'exclude_category' => isset( $_POST['orders_exclude_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['orders_exclude_category'] ) ) : array(),
				'include_category' => isset( $_POST['orders_include_category'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['orders_include_category'] ) ) : array(),
			),
		);

		if ( isset( $_POST['submit'] ) ) {
			if ( count( $wapi_settings_admin_args['custom']['row']['icon'] ) ) {
				foreach ( $wapi_settings_admin_args['custom']['row']['icon'] as $key => $val ) {
					if ( ! $wapi_settings_admin_args['custom']['row']['icon'][ $key ] || ! $wapi_settings_admin_args['custom']['row']['text'][ $key ] ) {
						unset( $wapi_settings_admin_args['custom']['row']['icon'][ $key ] );
						unset( $wapi_settings_admin_args['custom']['row']['text'][ $key ] );
						unset( $wapi_settings_admin_args['custom']['row']['url'][ $key ] );
					}
				}
			}
			update_option( '_wapi_settings', $wapi_settings_admin_args );
			?>
            <div class="updated">
                <p><?php esc_html_e( 'Your settings have been saved!', 'woo-advanced-product-information' ) ?></p>
            </div>
			<?php
		} elseif ( isset( $_POST['wapi_delete_option'] ) ) {
			if ( get_option( '_wapi_settings' ) ) {
				delete_option( '_wapi_settings' );
				delete_post_meta_by_key( '_wapi_settings' );
			}
			if ( ! empty( $_SERVER['REQUEST_URI'] ) ) {
				wp_safe_redirect( esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) );
				exit();
			}
		}
	}
}