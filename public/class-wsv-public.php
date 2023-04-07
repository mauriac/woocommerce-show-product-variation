<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Wsv
 * @subpackage Wsv/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wsv
 * @subpackage Wsv/public
 * @author     Mauriac Azoua <azouzmauriac@gmail.com>
 */
class Wsv_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wsv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wsv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wsv-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'wsv-dataTables-min', plugin_dir_url( __FILE__ ) . 'css/datatables.min.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wsv_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wsv_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wsv-public.js', array( 'jquery' ), $this->version, false );

	}

	public static function show_variations_by_shortcode( $query_args ) {
		$wsv_show_vari_on_shortcode = get_option( 'wsv_show_vari_on_shortcode' );
		if ( $wsv_show_vari_on_shortcode ) {
			$query_args['post_type'] = array( 'product', 'product_variation' );
		}

		return $query_args;
	}

	// public static function add_product_to_cart() {
	// $product_id  = filter_input( INPUT_POST, 'product_id' );
	// $product_qty = filter_input( INPUT_POST, 'product_qty' );
	// WC()->cart->add_to_cart( $product_id, $product_qty );
	// }

	public function product_query( $q ) {
		if ( 'sp' === get_option( 'wsv_show_vari_on_shop_cat' ) ) {
			$q->set( 'post_type', array( 'product', 'product_variation' ) );
			$wsv_exc_vari             = get_option( WSV_EXCEPT_SING_VARI );
			$wsv_exc_vari             = is_array( $wsv_exc_vari ) ? $wsv_exc_vari : array();
			$wsv_excludes_attributes  = (array) get_option( 'wsv_excludes_attributes', array() );
			$wsv_show_vari_keep_first = get_option( 'wsv_show_vari_keep_first' );
			$excl_vari                = array();

			$wsv_excludes_category = get_option( 'wsv_excludes_category' );
			if ( is_array( $wsv_excludes_category ) ) {
				$variable_list_by_cat = wc_get_products(
					array(
						'type'      => 'variable',
						'tax_query' => array(
							array(
								'taxonomy' => 'product_cat',
								'terms'    => $wsv_excludes_category,
								'operator' => 'IN',
							),
						),
						'limit'     => -1,
					)
				);

				if ( is_array( $variable_list_by_cat ) ) {
					foreach ( $variable_list_by_cat as $variable ) {
						$excl_vari = array_merge( $excl_vari, $variable->get_children() );
					}
				}
			}

			if ( is_array( $wsv_excludes_attributes ) ) {
				$variation_products = wc_get_products(
					array(
						'type'  => 'variation',
						'limit' => -1,
					)
				);
				$first_keep         = array();
				if ( is_array( $variation_products ) ) {
					foreach ( $variation_products as $variation ) {
						$variation_attributes = $variation->get_attributes();
						foreach ( $wsv_excludes_attributes as $excl_attribute_val ) {
							if ( isset( $variation_attributes[ $excl_attribute_val ] ) && ! empty( $variation_attributes[ $excl_attribute_val ] ) ) {

								if ( 'on' === $wsv_show_vari_keep_first ) {
									$parent_variation_attributes = wc_get_product( $variation->get_parent_id() )->get_variation_attributes();
									$parent_variation_attributes = array_change_key_case( $parent_variation_attributes );

									$current_attribute_values = null;
									if ( isset( $parent_variation_attributes[ strtolower( $excl_attribute_val ) ] ) ) {
										$current_attribute_values = $parent_variation_attributes[ strtolower( $excl_attribute_val ) ];
									}
									$first_attri = reset( $current_attribute_values );
									if ( $first_attri === $variation_attributes[ $excl_attribute_val ] ) {
										$first_keep[ $variation->get_parent_id() ] [] = $variation->get_id();
									}
								}

								if ( ! isset( $first_keep[ $variation->get_parent_id() ] ) || ! in_array( $variation->get_id(), $first_keep[ $variation->get_parent_id() ], true ) ) {
									$wsv_exc_vari[ $variation->get_parent_id() ][] = $variation->get_id();
								}
							}
						}
					}
				}
			}

			// exclude variable parent product.
			$wsv_hide_parent_product_variable = get_option( 'wsv_hide_parent_product_variable' );
			$wsv_exc_parent                   = array();
			if ( $wsv_hide_parent_product_variable ) {
				$variable_product = wc_get_products(
					array(
						'type'  => 'variable',
						'limit' => -1,
					)
				);

				$wsv_exc_parent = array_map(
					function( $o ) {
						return $o->get_id();
					},
					(array) $variable_product
				);
			} else {
				$wsv_exc_parent = get_option( WSV_EXC_PROD_PAR, array() );
			}
			// exclude single variation.
			foreach ( $wsv_exc_vari as $value ) {
				if ( is_array( $value ) ) {
					$excl_vari = isset( $excl_vari ) ? array_merge( $excl_vari, $value ) : $value;
				}
			}
			$excl_vari = isset( $wsv_exc_parent ) ? array_merge( $excl_vari, $wsv_exc_parent ) : $excl_vari;
			if ( is_product_category() ) {
				$paged = $q->query_vars ['paged'];
				if ( $paged > 1 ) {
					$q->query_vars ['paged'] = $paged - 1;
				}
				$products                = wc_get_products( $q->query_vars );
				$q->query_vars ['paged'] = $paged;

				if ( ! empty( $products ) ) {
					$products_id = array_map(
						function( $o ) {
							return $o->get_id();
						},
						(array) $products
					);
					foreach ( (array) $products as $prod ) {
						if ( 'variable' === $prod->get_type() ) {
							$products_id = array_merge( $products_id, $prod->get_children() );
						}
					}
					$q->set( 'product_cat', array() );
					$q->set( 'post__in', array_diff( $products_id, $excl_vari ) );
				}
			} else {
				$q->set( 'post__not_in', $excl_vari );
			}
		}

		return $q;
	}

	public function display_variation_as_dropdown( $add_to_cart_html, $product ) {
		if ( 'dp' !== get_option( 'wsv_show_vari_on_shop_cat' ) || 'variable' !== $product->get_type() || ! $product->has_child() ) {
			return $add_to_cart_html;
		}

		// Enqueue variation scripts.
		wp_enqueue_script( 'wc-add-to-cart-variation' );

		$available_variations = $product->get_available_variations();
		$attributes           = $product->get_variation_attributes();
		global $wp;

		$attribute_keys  = array_keys( $attributes );
		$variations_json = wp_json_encode( $available_variations );
		$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

		?>
		<form class="variations_form cart" action="<?php echo esc_url( home_url( $wp->request ) ); ?>" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo $variations_attr; // WPCS: XSS ok. ?>">
			<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
				<p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
			<?php else : ?>
				<table class="variations" cellspacing="0">
					<tbody>
						<?php foreach ( $attributes as $attribute_name => $options ) : ?>
							<tr>
								<td class="label"><label for="<?php echo esc_attr( sanitize_title( $attribute_name ) ); ?>"><?php echo wc_attribute_label( $attribute_name ); // WPCS: XSS ok. ?></label></td>
								<td class="value">
									<?php
										wc_dropdown_variation_attribute_options(
											array(
												'options' => $options,
												'attribute' => $attribute_name,
												'product' => $product,
											)
										);
										echo end( $attribute_keys ) === $attribute_name ? wp_kses_post( apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__( 'Clear', 'woocommerce' ) . '</a>' ) ) : '';
									?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<div class="single_variation_wrap">
					<?php
						/**
						 * Hook: woocommerce_before_single_variation.
						 */
						do_action( 'woocommerce_before_single_variation' );

						/**
						 * Hook: woocommerce_single_variation. Used to output the cart button and placeholder for variation data.
						 *
						 * @since 2.4.0
						 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
						 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
						 */
						do_action( 'woocommerce_single_variation' );
					?>
				</div>
			<?php endif; ?>
		</form>
		<?php
	}

	public function display_product_title( $name, $product ) {
		$custom_name = get_post_meta( $product->get_id(), 'wsv_custom_name', true );
		if ( $custom_name ) {
			return $custom_name;
		}
		return $name;
	}

	/**
	 * Show lowest/highest prices for variable products
	 */
	public function show_variation_price_format( $price, $product ) {

		$wsv_show_vari_lh_price = get_option( 'wsv_show_vari_lh_price', null );
		if ( $wsv_show_vari_lh_price ) {
			// Main Price
			$prices = array( $product->get_variation_price( 'min', true ), $product->get_variation_price( 'max', true ) );
			if ( 'lowest' === $wsv_show_vari_lh_price ) {
				$price = $prices[0] !== $prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $prices[0] ) ) : wc_price( $prices[0] );
				// Sale Price
				$regular_prices = array( $product->get_variation_regular_price( 'min', true ), $product->get_variation_regular_price( 'max', true ) );
				sort( $regular_prices );
				$saleprice = $regular_prices[0] !== $regular_prices[1] ? sprintf( __( 'From: %1$s', 'woocommerce' ), wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );
				if ( $price !== $saleprice ) {
					$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . wc_price( $prices[0] ) . $product->get_price_suffix() . '</ins>';
				}
			} elseif ( 'highest' === $wsv_show_vari_lh_price ) {
				$price = $prices[0] !== $prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $prices[1] ) ) : wc_price( $prices[1] );
				// Sale Price
				$regular_prices = array( $product->get_variation_regular_price( 'max', true ), $product->get_variation_regular_price( 'min', true ) );
				sort( $regular_prices );
				$saleprice = $regular_prices[0] !== $regular_prices[1] ? sprintf( __( 'Up To: %1$s', 'woocommerce' ), wc_price( $regular_prices[0] ) ) : wc_price( $regular_prices[0] );
				if ( $price !== $saleprice ) {
					$price = '<del>' . $saleprice . $product->get_price_suffix() . '</del> <ins>' . wc_price( $prices[1] ) . $product->get_price_suffix() . '</ins>';
				}
			}
		}
		return $price;
	}

}
