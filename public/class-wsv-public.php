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

		wp_enqueue_script( 'wsv-datatables-min', plugin_dir_url( __FILE__ ) . 'js/datatables.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wsv-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'ajaxurl', admin_url( 'admin-ajax.php' ) );

	}

	public static function show_variations_by_shortcode( $query_args ) {
		// $query_args['post_type'] = array( 'product', 'product_variation' );

		return $query_args;
	}


	public static function get_variations_table() {
		$wsv_enable_var_table_show = get_option( 'wsv_enable_var_table_show' );
		if ( ! $wsv_enable_var_table_show ) {
			return;
		}
		global $product;
		if ( 'variable' !== $product->get_type() || ! $product->has_child() ) {
			return;
		}
		$id = $product->get_id();

		$product = wc_get_product( $id );
		$vars_id = $product->get_children();
		?>
			<div class="row">
				<table id="example2" cellspacing="0">
					<thead>
						<tr>
							<th class="product-thumbnail"><?php esc_html_e( 'Thumbnail', 'woocommerce' ); ?></th>
							<th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
							<th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
							<th class="product-quantity"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></th>
							<th class="product-subtotal"><?php esc_html_e( 'Add to cart', 'woocommerce' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $vars_id as $key => $var_id ) {
							$variation = wc_get_product( $var_id );

							if ( $variation && $variation->exists() ) {
								$product_permalink = $variation->is_visible() ? $variation->get_permalink() : '';
								?>
								<tr >

									<td class="product-thumbnail">
										<?php

										$thumbnail = $variation->get_image();

										if ( ! $product_permalink ) {
											echo $thumbnail; // PHPCS: XSS ok.
										} else {
											printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
										}
										?>
									</td>

									<td class="product-name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
										<?php
										if ( ! $product_permalink ) {
											echo wp_kses_post( $variation->get_formatted_name() );
										} else {
											echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $variation->get_formatted_name() ) );
										}
										?>
									</td>

									<td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
										<?php
											echo $variation->get_price();
										?>
									</td>

									<td class="product-quantity" data-title="<?php esc_attr_e( 'Quantity', 'woocommerce' ); ?>">
										<?php
										if ( $variation->is_sold_individually() ) {
											$product_quantity = sprintf( '1 <input type="hidden" class="wsv_quantity" name="wsv-quantity" value="1" />' );
										} else {
											$product_quantity = woocommerce_quantity_input(
												array(
													'input_name'   => 'wsv-quantity',
													'classes'   => 'wsv_quantity',
													'placeholder'   => $var_id,
												)
											);
										}
											echo $product_quantity;
										?>
									</td>

									<td>
										<a href="?add-to-cart=<?php echo $var_id; ?>" data-quantity = "1" 
											class="button wsv-add-to-cart wsv-add-to-cart-<?php echo $var_id; ?>" 
											data-product_id="<?php echo $var_id; ?>"> <?php esc_attr_e( 'Add to cart', 'woocommerce' ); ?></a>
									</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
			</div>
		<?php
	}

	public static function add_product_to_cart() {
		$product_id  = filter_input( INPUT_POST, 'product_id' );
		$product_qty = filter_input( INPUT_POST, 'product_qty' );
		WC()->cart->add_to_cart( $product_id, $product_qty );
	}

	public function product_query( $q ) {
		if ( 'sp' === get_option( 'wsv_show_vari_on_shop_cat' ) ) {
			$q->set( 'post_type', array( 'product', 'product_variation' ) );
			$wsv_exc_vari   = get_option( WSV_EXCEPT_SING_VARI );
			$wsv_exc_parent = get_option( WSV_EXC_PROD_PAR );
			$wsv_exc_vari   = is_array( $wsv_exc_vari ) ? $wsv_exc_vari : array();
			$wsv_exc_parent = is_array( $wsv_exc_parent ) ? $wsv_exc_parent : array();
			$excl_vari = array();
			foreach ( $wsv_exc_vari as $value ) {
				if ( is_array( $value ) ) {
					$excl_vari = isset( $excl_vari ) ? array_merge( $excl_vari, $value ) : $value;
				}
			}
			$excl_vari = isset( $wsv_exc_parent ) ? array_merge($excl_vari, $wsv_exc_parent ) : $excl_vari;
			$q->set( 'post__not_in', $excl_vari + $wsv_exc_parent );
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
}
