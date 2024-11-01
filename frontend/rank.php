<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_ADVANCED_PRODUCT_INFORMATION_Frontend_Rank {
	protected $settings;

	function __construct() {
		$data           = new WC_ADVANCED_PRODUCT_INFORMATION_Data();
		$this->settings = array_merge( array(
			'enable'           => "on",
			'mobile'           => "on",
			'position'         => "after_title",
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
		), $data->get_params( 'rank' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ) );
	}

	public function frontend_enqueue() {
		global $post;
		if ( ! $this->settings || 'on' != $this->settings['enable'] ) {
			return;
		}
//		if ( is_tax( 'product_cat' ) || is_post_type_archive( 'product' ) ) {
//
//			wp_enqueue_style( 'wapinfo-frontend-rank-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/rank.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
//			$css = '';
//			$i   = 0;
//			$css .= '.rank-in-category{';
//			if ( $this->settings['text_align'] ) {
//				$css .= 'text-align:' . $this->settings['text_align'] . ';';
//			}
//			if ( $this->settings['border_color'] ) {
//				$i ++;
//				$css .= 'border:1px solid ' . $this->settings['border_color'] . ';';
//			}
//			if ( $this->settings['border_radius'] ) {
//				$css .= 'border-radius:' . $this->settings['border_radius'] . 'px;';
//			}
//			if ( $this->settings['text_color'] ) {
//				$css .= 'color:' . $this->settings['text_color'] . ';';
//			}
//			if ( $this->settings['text_bg_color'] ) {
//				$i ++;
//				$css .= 'background-color:' . $this->settings['text_bg_color'] . ';';
//			}
//			if ( $i > 0 ) {
//				$css .= 'padding: 5px 10px;';
//			}
//			$css .= '}';
//			if ( isset( $this->settings['css'] ) && $this->settings['css'] ) {
//				$css .= $this->settings['css'];
//			}
//			wp_add_inline_style( 'wapinfo-frontend-rank-style', $css );
//			add_action( 'woocommerce_after_template_part', array( $this, 'countdown_after_template_loop' ) );
//		}
		if ( is_product() ) {
			if ( ! $this->condition( $post->ID ) ) {
				return;
			}

			wp_enqueue_style( 'wapinfo-frontend-rank-style', VI_WC_ADVANCED_PRODUCT_INFORMATION_CSS . 'frontend/rank.css', array(), VI_WC_ADVANCED_PRODUCT_INFORMATION_VERSION );
			$css = '';
			$i   = 0;
			$css .= '.rank-in-category{';
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
			wp_add_inline_style( 'wapinfo-frontend-rank-style', $css );
			if ( 'after_cart' == $this->settings['position'] ) {
				add_action( 'woocommerce_after_add_to_cart_form', array( $this, 'rank' ) );
			} else {
				add_action( 'woocommerce_after_template_part', array( $this, 'rank_after' ) );

			}
		}
	}

	public function countdown_after_template_loop( $template_name ) {
		if ( ! in_array( $template_name, array(
			'loop/price.php',
		), true ) ) {
			return;
		}

		global $product;
		$product_id = $product->get_id();
		if ( ! $this->condition( $product_id ) ) {
			return;
		}
		$today        = getdate();
		$week         = absint( ( $today['yday'] ) / 7 );
		$month        = $today['mon'];
		$year         = $today['year'];
		$min          = absint( $this->settings['min'] );
		$rank         = 1;
		$rank_in_cats = array();
		foreach ( $product->get_category_ids() as $cat ) {
			$cat_children = get_term_children( $cat, 'product_cat' );
//			skip categories that have children
			if ( is_array( $cat_children ) && count( $cat_children ) ) {
				continue;
			}
			if ( 'alltime' === $this->settings['by'] ) {
				$tday         = strtotime( 'today' );
				$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				if ( ! $rank_alltime || $rank_alltime['day'] != $tday ) {
					self::calculate_rank_alltime( $cat );
					$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				}

				foreach ( $rank_alltime['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'week' === $this->settings['by'] ) {
				$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				if ( ! $rank_week || $rank_week['week'] != $week || $rank_week['year'] != $year ) {
					self::calculate_rank_week( $cat );
					$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				}

				foreach ( $rank_week['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'month' === $this->settings['by'] ) {
				$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				if ( ! $rank_month || $rank_month['month'] != $month || $rank_month['year'] != $year ) {
					self::calculate_rank_month( $cat );
					$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				}
				foreach ( $rank_month['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			}
			if ( count( $rank_in_cats ) == 1 ) {
				break;
			}
			if ( $rank <= $min ) {
				$rank_in_cats[] = array(
					'rank' => $rank,
					'cat'  => $cat
				);
			}
		}
		asort( $rank_in_cats );
		$time = "";
		if ( 'alltime' !== $this->settings['by'] ) {
			$time = $this->settings['by'];
			$time = ' last ' . $time;
		}
		if ( count( $rank_in_cats ) ) {
			echo '<div class="rank-in-category-wrap">';
			foreach ( $rank_in_cats as $rank_in_cat ) {
				$text = stripslashes( $this->settings['text'] );
				$text = str_replace( '{rank}', '<span class="wapinfo-rank-in-cat wapinfo-rank-in-cat-' . $rank_in_cat['rank'] . '">' . $rank_in_cat['rank'] . '</span>', $text );
				$text = str_replace( '{category}', '<a href="' . get_term_link( $rank_in_cat['cat'], 'product_cat' ) . '" target="blank">' . get_term( $rank_in_cat['cat'] )->name . '</a>', $text );
				$text = str_replace( '{time}', $time, $text );
				echo '<div class="rank-in-category">' . do_shortcode( $text ) . '</div>';
			}
			echo '</div>';
		}
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

	public function rank() {
		global $product;
		$product_id   = $product->get_id();
		$today        = getdate();
		$week         = absint( ( $today['yday'] ) / 7 );
		$month        = $today['mon'];
		$year         = $today['year'];
		$min          = absint( $this->settings['min'] );
		$rank         = 1;
		$rank_in_cats = array();
		foreach ( $product->get_category_ids() as $cat ) {
			$cat_children = get_term_children( $cat, 'product_cat' );
//			skip categories that have children
			if ( is_array( $cat_children ) && count( $cat_children ) ) {
				continue;
			}
			if ( 'alltime' === $this->settings['by'] ) {
				$tday         = strtotime( 'today' );
				$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				if ( ! $rank_alltime || $rank_alltime['day'] != $tday ) {
					self::calculate_rank_alltime( $cat );
					$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				}

				foreach ( $rank_alltime['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'week' === $this->settings['by'] ) {
				$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				if ( ! $rank_week || $rank_week['week'] != $week || $rank_week['year'] != $year ) {
					self::calculate_rank_week( $cat );
					$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				}

				foreach ( $rank_week['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'month' === $this->settings['by'] ) {
				$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				if ( ! $rank_month || $rank_month['month'] != $month || $rank_month['year'] != $year ) {
					self::calculate_rank_month( $cat );
					$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				}
				foreach ( $rank_month['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			}
			if ( count( $rank_in_cats ) == $this->settings['catnum'] ) {
				break;
			}
			if ( $rank <= $min ) {
				$rank_in_cats[] = array(
					'rank' => $rank,
					'cat'  => $cat
				);
			}
		}
		asort( $rank_in_cats );
		$time = "";
		if ( 'alltime' !== $this->settings['by'] ) {
			$time = $this->settings['by'];
			$time = ' last ' . $time;
		}
		if ( count( $rank_in_cats ) ) {
			echo '<div class="rank-in-category-wrap">';
			foreach ( $rank_in_cats as $rank_in_cat ) {
				$text = stripslashes( $this->settings['text'] );
				$text = str_replace( '{rank}', '<span class="wapinfo-rank-in-cat wapinfo-rank-in-cat-' . $rank_in_cat['rank'] . '">' . $rank_in_cat['rank'] . '</span>', $text );
				$text = str_replace( '{category}', '<a href="' . get_term_link( $rank_in_cat['cat'], 'product_cat' ) . '" target="blank">' . get_term( $rank_in_cat['cat'] )->name . '</a>', $text );
				$text = str_replace( '{time}', $time, $text );
				echo '<div class="rank-in-category">' . do_shortcode( $text ) . '</div>';
			}
			echo '</div>';
		}
	}

	public function rank_after( $template_name ) {
		if ( 'single-product/title.php' !== $template_name ) {
			return;
		}
		global $product;
		$product_id   = $product->get_id();
		$today        = getdate();
		$week         = absint( ( $today['yday'] ) / 7 );
		$month        = $today['mon'];
		$year         = $today['year'];
		$min          = absint( $this->settings['min'] );
		$rank         = 1;
		$rank_in_cats = array();
		foreach ( $product->get_category_ids() as $cat ) {
			$cat_children = get_term_children( $cat, 'product_cat' );
//			skip categories that have children
			if ( is_array( $cat_children ) && count( $cat_children ) ) {
				continue;
			}
			if ( 'alltime' === $this->settings['by'] ) {
				$tday         = strtotime( 'today' );
				$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				if ( ! $rank_alltime || $rank_alltime['day'] != $tday ) {
					self::calculate_rank_alltime( $cat );
					$rank_alltime = get_term_meta( $cat, 'wapi_category_rank_alltime', true );
				}

				foreach ( $rank_alltime['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'week' === $this->settings['by'] ) {
				$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				if ( ! $rank_week || $rank_week['week'] != $week || $rank_week['year'] != $year ) {
					self::calculate_rank_week( $cat );
					$rank_week = get_term_meta( $cat, 'wapi_category_rank_week', true );
				}

				foreach ( $rank_week['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			} elseif ( 'month' === $this->settings['by'] ) {
				$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				if ( ! $rank_month || $rank_month['month'] != $month || $rank_month['year'] != $year ) {
					self::calculate_rank_month( $cat );
					$rank_month = get_term_meta( $cat, 'wapi_category_rank_month', true );
				}
				foreach ( $rank_month['ranks'] as $r ) {
					if ( $r['p_id'] == $product_id ) {
						$rank = $r['p_rank'];
						break;
					}
				}
			}
			if ( count( $rank_in_cats ) == $this->settings['catnum'] ) {
				break;
			}
			if ( $rank <= $min ) {
				$rank_in_cats[] = array(
					'rank' => $rank,
					'cat'  => $cat
				);
			}
		}
		asort( $rank_in_cats );
		$time = "";
		if ( 'alltime' !== $this->settings['by'] ) {
			$time = $this->settings['by'];
			$time = ' last ' . $time;
		}
		if ( count( $rank_in_cats ) ) {
			echo '<div class="rank-in-category-wrap">';
			foreach ( $rank_in_cats as $rank_in_cat ) {
				$text = stripslashes( $this->settings['text'] );
				$text = str_replace( '{rank}', '<span class="wapinfo-rank-in-cat wapinfo-rank-in-cat-' . $rank_in_cat['rank'] . '">' . $rank_in_cat['rank'] . '</span>', $text );
				$text = str_replace( '{category}', '<a href="' . get_term_link( $rank_in_cat['cat'], 'product_cat' ) . '" target="blank">' . get_term( $rank_in_cat['cat'] )->name . '</a>', $text );
				$text = str_replace( '{time}', $time, $text );
				echo '<div class="rank-in-category">' . do_shortcode( $text ) . '</div>';
			}
			echo '</div>';
		}
	}

	public static function calculate_rank_alltime( $cat ) {
		$today        = strtotime( 'today' );
		$args         = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'product_cat'    => get_term( $cat )->slug,
			'posts_per_page' => - 1
		);
		$query        = new WP_Query( $args );
		$products     = array();
		$sold_alltime = array();
//		calculate sold quantity
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product_id   = get_the_ID();
				$product_data = wc_get_product( $product_id );
				$sold         = get_post_meta( $product_id, '_wapi_sold_quantity_alltime', true );
				if ( ! $sold || $sold['day'] != $today ) {
					$sold = array(
						'alltime' => $product_data->get_total_sales(),
						'day'     => $today,
					);
					update_post_meta( $product_id, '_wapi_sold_quantity_alltime', $sold );
				}
				$sold_alltime[] = $sold;
				$products[]     = $product_id;
			}
			wp_reset_postdata();
		}

		if ( count( $products ) ) {
			$ranks_alltime = array();
			foreach ( $products as $key1 => $product1 ) {
				$rank_alltime = 1;
				$temp_m       = array();
				foreach ( $products as $key2 => $product2 ) {
					if ( ! in_array( $sold_alltime[ $key2 ]['alltime'], $temp_m, true ) ) {
						$temp_m[] = $sold_alltime[ $key2 ]['alltime'];
						if ( $sold_alltime[ $key1 ]['alltime'] < $sold_alltime[ $key2 ]['alltime'] ) {
							$rank_alltime ++;
						}
					}
				}
				$ranks_alltime[] = array(
					'p_id'   => $product1,
					'p_rank' => $rank_alltime,
				);
			}
			update_term_meta( $cat, 'wapi_category_rank_alltime', array(
				'ranks' => $ranks_alltime,
				'day'   => $today
			) );
		}
	}

	public static function calculate_rank_week( $cat ) {
		$today           = getdate();
		$week            = absint( $today['yday'] / 7 );
		$year            = $today['year'];
		$last_week       = strtotime( "last week" );
		$last_week       = strtotime( gmdate( 'Y-m-d', $last_week ) );
		$this_week       = $last_week + 6 * 86400;
		$args            = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'product_cat'    => get_term( $cat )->slug,
			'posts_per_page' => - 1
		);
		$query           = new WP_Query( $args );
		$products        = array();
		$sold_last_weeks = array();
//		calculate sold quantity
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product_id = get_the_ID();
				$sold       = get_post_meta( $product_id, '_wapi_sold_quantity_lastweek', true );
				if ( ! $sold || $sold['week'] != $week || $sold['year'] != $year ) {
					$sold = array(
						'lastweek' => self::sold_quantity( $product_id, $last_week, $this_week ),
						'week'     => $week,
						'year'     => $year
					);
					update_post_meta( $product_id, '_wapi_sold_quantity_lastweek', $sold );
				}
				$sold_last_weeks[] = $sold;
				$products[]        = $product_id;
			}
			wp_reset_postdata();
		}
		if ( count( $products ) ) {
			$ranks_week = array();
			foreach ( $products as $key1 => $product1 ) {
				$rank_week = 1;
				$temp_m    = array();
				foreach ( $products as $key2 => $product2 ) {
					if ( ! in_array( $sold_last_weeks[ $key2 ]['lastweek'], $temp_m, true ) ) {
						$temp_m[] = $sold_last_weeks[ $key2 ]['lastweek'];
						if ( $sold_last_weeks[ $key1 ]['lastweek'] < $sold_last_weeks[ $key2 ]['lastweek'] ) {
							$rank_week ++;
						}
					}
				}
				$ranks_week[] = array(
					'p_id'   => $product1,
					'p_rank' => $rank_week,
				);
			}
			update_term_meta( $cat, 'wapi_category_rank_week', array(
				'ranks' => $ranks_week,
				'week'  => $week,
				'year'  => $year
			) );
		}
	}

	public static function sold_quantity( $product_id, $from, $to ) {
		$qty  = 0;
		$args = array(
			'post_type'      => 'shop_order',
			'post_status'    => array( 'wc-on-hold', 'wc-completed', 'wc-processing' ),
			'posts_per_page' => - 1,
			'date_query'     => array(
				array(
					'after'     => gmdate( 'Y-m-d', $from ),
					'before'    => gmdate( 'Y-m-d', $to ),
					'inclusive' => true,
				)
			)
		);

		$orders = wc_get_orders( $args );

		if ( ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				foreach ( $order->get_items() as $item_data ) {
					if ( $product_id == $item_data->get_product_id() ) {
						$qty += $item_data->get_quantity();
					}
				};
			}
		}

		return $qty;
	}

	public static function calculate_rank_month( $cat ) {
		$today            = getdate();
		$month            = $today['mon'];
		$year             = $today['year'];
		$last_month       = strtotime( $today['year'] . '-' . ( $today['mon'] - 1 ) . '-1' );
		$this_month       = strtotime( $today['year'] . '-' . $today['mon'] . '-1' ) - 86400;
		$args             = array(
			'post_type'      => 'product',
			'post_status'    => 'publish',
			'product_cat'    => get_term( $cat )->slug,
			'posts_per_page' => - 1
		);
		$query            = new WP_Query( $args );
		$products         = array();
		$sold_last_months = array();
//		calculate sold quantity
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$product_id = get_the_ID();
				$sold       = get_post_meta( $product_id, '_wapi_sold_quantity_lastmonth', true );
				if ( ! $sold || $sold['month'] != $month || $sold['year'] != $year ) {
					$sold = array(
						'lastmonth' => self::sold_quantity( $product_id, $last_month, $this_month ),
						'month'     => $month,
						'year'      => $year
					);
					update_post_meta( $product_id, '_wapi_sold_quantity_lastmonth', $sold );
				}
				$sold_last_months[] = $sold;
				$products[]         = $product_id;
			}
			wp_reset_postdata();
		}

		if ( count( $products ) ) {
			$ranks_month = array();
			foreach ( $products as $key1 => $product1 ) {
				$rank_month = 1;
				$temp_m     = array();
				foreach ( $products as $key2 => $product2 ) {
					if ( ! in_array( $sold_last_months[ $key2 ]['lastmonth'], $temp_m, true ) ) {
						$temp_m[] = $sold_last_months[ $key2 ]['lastmonth'];
						if ( $sold_last_months[ $key1 ]['lastmonth'] < $sold_last_months[ $key2 ]['lastmonth'] ) {
							$rank_month ++;
						}
					}
				}
				$ranks_month[] = array(
					'p_id'   => $product1,
					'p_rank' => $rank_month,
				);
			}
			update_term_meta( $cat, 'wapi_category_rank_month', array(
				'ranks' => $ranks_month,
				'month' => $month,
				'year'  => $year
			) );
		}
	}
}