<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Wsv
 * @subpackage Wsv/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wsv
 * @subpackage Wsv/admin
 * @author     Mauriac Azoua <azouzmauriac@gmail.com>
 */
class Wsv_Admin {

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
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wsv-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wsv-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function is_woocommerce_enabled() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'get_woocommerce_disabled_notice' ) );
			return;
		}
	}

	public function get_woocommerce_disabled_notice() {
		echo '<div class="error"><p><strong>' . esc_attr__( WSV_PLUGIN_NAME, 'show-product-variations-for-woocommerce' ) . '</strong> ' . sprintf( __( 'requires %1$sWooCommerce%2$s to be installed & activated!', 'show-product-variations-for-woocommerce' ), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>' ) . '</p></div>';
	}

	public function add_wsv_menu() {
		add_submenu_page( 'woocommerce', 'Wc Show Variation Settings', 'Wc Show Variation Settings', 'manage_options', 'show-product-variations-for-woocommerce', array( $this, 'configure_settings_page' ) );
	}

	public static function get_single_variation_settings_page() {
		if ( ( isset( $_POST['wsv-settings'], $_POST['securite_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['securite_nonce'] ), 'wsv_securite' ) ) ) {
			if ( ! isset( $_POST['wsv-settings']['wsv_show_vari_on_shortcode'] ) ) {
				$_POST['wsv-settings']['wsv_show_vari_on_shortcode'] = null;
			}
			if ( ! isset( $_POST['wsv-settings']['wsv_excludes_category'] ) ) {
				$_POST['wsv-settings']['wsv_excludes_category'] = null;
			}
			if ( ! isset( $_POST['wsv-settings']['wsv_hide_parent_product_variable'] ) ) {
				$_POST['wsv-settings']['wsv_hide_parent_product_variable'] = null;
			}
			if ( ! isset( $_POST['wsv-settings']['wsv_excludes_attributes'] ) ) {
				$_POST['wsv-settings']['wsv_excludes_attributes'] = null;
			}
			if ( ! isset( $_POST['wsv-settings']['wsv_show_vari_keep_first'] ) ) {
				$_POST['wsv-settings']['wsv_show_vari_keep_first'] = null;
			}
			if ( ! isset( $_POST['wsv-settings']['wsv_show_vari_lh_price'] ) ) {
				$_POST['wsv-settings']['wsv_show_vari_lh_price'] = null;
			}
			wsv_update_option( wp_unslash( $_POST['wsv-settings'] ) );

			?>
			<div class="wad notice notice-success is-dismissible">
				<p>
					<?php
						echo '<b>' . esc_attr__( WSV_PLUGIN_NAME, 'show-product-variations-for-woocommerce' ) . '</b>' . sprintf( __( ': Data saved successful!', 'show-product-variations-for-woocommerce' ) );
					?>
				</p>
			</div>
			<?php
		}

		$variable_products = wc_get_products(
			array(
				'type'  => 'variable',
				'limit' => -1,
			)
		);
		$attributes_values = array();

		$all_attributes = array_map(
			function( $o ) {
				$attributes = $o->get_attributes();

				return array_keys( $attributes );
			},
			$variable_products
		);
		foreach ( $all_attributes as $attributes_array ) {
			$attributes_values = is_array( $attributes_array ) ? array_merge( $attributes_values, $attributes_array ) : $attributes_values;
		}
		$attributes_values = is_array( $attributes_values ) ? array_unique( $attributes_values ) : null;

		$args               = array(
			'taxonomy' => 'product_cat',
		);
		$product_categories = get_terms( $args );

		$wsv_show_vari_on_shop_cat        = get_option( 'wsv_show_vari_on_shop_cat' );
		$wsv_show_vari_on_shortcode       = get_option( 'wsv_show_vari_on_shortcode' );
		$wsv_hide_parent_product_variable = get_option( 'wsv_hide_parent_product_variable' );
		$wsv_excludes_category            = (array) get_option( 'wsv_excludes_category', array() );
		$wsv_excludes_attributes          = (array) get_option( 'wsv_excludes_attributes', array() );
		$wsv_show_vari_keep_first         = get_option( 'wsv_show_vari_keep_first' );
		$wsv_show_vari_lh_price           = get_option( 'wsv_show_vari_lh_price' );
		?>
		<div class="wrap">
			<form method="POST">
				<table class="form-table">
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_attr_e( 'Show Variations On Shop & Category As', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
							</th>
							<td>
								<select name="wsv-settings[wsv_show_vari_on_shop_cat]">
									<option value="no"
										<?php
											echo ( ( 'no' === $wsv_show_vari_on_shop_cat ) ? 'selected' : '' );
										?>
										>
									</option>
									<option value="sp" 
										<?php
											echo ( ( 'sp' === $wsv_show_vari_on_shop_cat ) ? 'selected' : '' );
										?>
										>
										<?php esc_attr_e( 'Single Product', 'show-product-variations-for-woocommerce' ); ?>
									</option>
									<option value="dp" 
										<?php
											echo ( ( 'dp' === $wsv_show_vari_on_shop_cat ) ? 'selected' : '' );
										?>
										>
										<?php esc_attr_e( 'Dropdown', 'show-product-variations-for-woocommerce' ); ?>
									</option>
								</select>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_html_e( 'Show variations in shortcodes', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
							</th>
							<td>
								<div class="form-check">
									<input name="wsv-settings[wsv_show_vari_on_shortcode]" type="checkbox" class="form-check-input" id="exampleCheck1"
										<?php
											echo ( ( 'on' === $wsv_show_vari_on_shortcode ) ? 'checked' : ' ' );
										?>
									>
								</div>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_attr_e( 'Hide Parent Product of Variable Product', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
							</th>
							<td>
								<div class="form-check">
									<input name="wsv-settings[wsv_hide_parent_product_variable]" type="checkbox" class="form-check-input"
										<?php
											echo ( ( 'on' === $wsv_hide_parent_product_variable ) ? 'checked' : ' ' );
										?>
									>
								</div>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong> 
									<?php esc_html_e( 'Exclude Category', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
							</th>
							<td>
								<select multiple name="wsv-settings[wsv_excludes_category][]">
									<?php foreach ( $product_categories as $wp_term ) : ?>
										<?php
											$value    = $wp_term->term_id;
											$selected = '';
										if ( in_array( $value, $wsv_excludes_category, true ) ) {
											$selected = 'selected';
										}
										?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_attr( $wp_term->name ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_html_e( 'Exclude Attribute Taxonomies', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
								<br>
								<small  style="font-weight: initial;">
									<?php esc_html_e( 'Variations assigned to this category will not appear. Create an "Any..." variation to still show variations.', 'show-product-variations-for-woocommerce' ); ?>
								</small>
							</th>
							<td>
								<select multiple name="wsv-settings[wsv_excludes_attributes][]">
									<?php
									foreach ( $attributes_values as $value ) {
										$selected = '';
										if ( is_array( $wsv_excludes_category ) && in_array( $value, $wsv_excludes_attributes, true ) ) {
											$selected = 'selected';
										}
										?>
										<option value="<?php echo esc_attr( $value ); ?>" <?php echo esc_attr( $selected ); ?> ><?php echo esc_attr( $value ); ?></option>
										<?php
									}
									?>
								</select>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_html_e( 'Keep First Variation', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
								<br>
								<small style="font-weight: initial;">
									<?php esc_html_e( 'First variation of each attribute term will be displayed.', 'show-product-variations-for-woocommerce' ); ?>
								</small>
							</th>
							<td>
								<div class="form-check">
									<input name="wsv-settings[wsv_show_vari_keep_first]" type="checkbox" class="form-check-input" 
										<?php
											echo ( ( 'on' === $wsv_show_vari_keep_first ) ? 'checked' : ' ' );
										?>
									>
								</div>
							</td>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<h3 class="wc-settings-sub-title ">
									<?php
										esc_html_e( 'Variation Price', 'show-product-variations-for-woocommerce' );
									?>
								</h3>
							</th>
						</div>
					</tr>
					<tr valign="top">
						<div class="col-auto my-1">
							<th scope="row">
								<strong>
									<?php esc_html_e( 'Show Lowest/Highest Price', 'show-product-variations-for-woocommerce' ); ?>
								</strong>
								<br>
								<small style="font-weight: initial;">
									<?php esc_html_e( 'Show lowest/highest variation price instead of price range.', 'show-product-variations-for-woocommerce' ); ?>
								</small>
							</th>
							<td>
								<div class="form-check">
								<select name="wsv-settings[wsv_show_vari_lh_price]">
									<option value="no"
										<?php
											echo ( ( 'no' === $wsv_show_vari_lh_price ) ? 'selected' : '' );
										?>
										>
									</option>
									<option value="lowest" 
										<?php
											echo ( ( 'lowest' === $wsv_show_vari_lh_price ) ? 'selected' : '' );
										?>
										>
										<?php esc_attr_e( 'Lowest', 'show-product-variations-for-woocommerce' ); ?>
									</option>
									<option value="highest" 
										<?php
											echo ( ( 'highest' === $wsv_show_vari_lh_price ) ? 'selected' : '' );
										?>
										>
										<?php esc_html_e( 'Highest', 'show-product-variations-for-woocommerce' ); ?>
									</option>
								</select>
								</div>
							</td>
						</div>
					</tr>
				</table>
				<input type="hidden" name="securite_nonce" value="<?php echo esc_html( wp_create_nonce( 'wsv_securite' ) ); ?>"/>
				<span ><?php submit_button(); ?></span>
			</form>
		</div>
		<?php
	}

	public function configure_settings_page() {
		?>
		<?php
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = filter_input( INPUT_GET, 'tab' );
		} else {
			$active_tab = 'variations-options';
		}
		?>
		<div class="wrap">
			<h1 style="font-size: 23px; text-transform: uppercase; margin: 1em 0;"><?php esc_html_e( 'Wc Show Variation Settings', 'show-product-variations-for-woocommerce' ); ?></h1>
			<div>
				<p>
					<?php printf( esc_html__( 'Thank you for using our plugin! Would you please show us a little love by rating us in the WordPress.org? %s ', 'show-product-variations-for-woocommerce' ), '<span style="color:#ffb900">&#9733;&#9733;&#9733;&#9733;&#9733;</span>' ); ?>
					<br/>
					<a href="<?php echo esc_url( WSV_REVIEWS ); ?>"
						target="_blank"><?php esc_html_e( 'Reviews', 'show-product-variations-for-woocommerce' ); ?></a>
				</p>
			</div>
			<h2 class="nav-tab-wrapper">
				<a href="?page=wsv&tab=variations-options"  class="nav-tab <?php echo 'variations-options' === $active_tab ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Single Variation Options', 'show-product-variations-for-woocommerce' ); ?></a>
			</h2>
				<?php
				if ( 'variations-options' === $active_tab ) {
					?>
				<div class="woousn-options-tab">
					<?php
						$this->get_single_variation_settings_page();
					?>
				</div>
					<?php
				}
				?>
		</div>
		<?php
	}

	public function get_product_tab_data() {
		?>
		<div id='wsv_tab_content' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<?php
					woocommerce_wp_checkbox(
						array(
							'id'          => WSV_EXCEPT_SING_VARI,
							'label'       => __( 'Exclude Single Variation', 'show-product-variations-for-woocommerce' ),
							'description' => __( 'This option will exclude single variation on shop & category pages.', 'show-product-variations-for-woocommerce' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'          => WSV_EXC_PROD_PAR,
							'label'       => __( 'Hide Parent Variable Product', 'show-product-variations-for-woocommerce' ),
							'description' => __( 'Enable this option to Hide parent variation on shop & category pages.<br/><strong>Note: this option will be not work for Show Variations Dropdown</strong>', 'show-product-variations-for-woocommerce' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'          => WSV_EXC_PROD_TABLE,
							'label'       => __( 'Hide Variations Table', 'show-product-variations-for-woocommerce' ),
							'description' => __( 'Enable this option to Hide variations table on this product page.', 'show-product-variations-for-woocommerce' ),
						)
					);
				?>
		</div>
	</div>
		<?php
	}
	public function get_product_tab_label( $tabs ) {
		if ( ! is_array( $tabs ) ) {
			return;
		}
		$tabs['wsv_tab'] = array(
			'label'  => __( 'Show Variations', 'show-product-variations-for-woocommerce' ),
			'target' => 'wsv_tab_content',
			'class'  => array(),
		);
		return $tabs;
	}

	public function save_post( $post_id ) {
		$product = wc_get_product( $post_id );

		if ( 'variable' !== $product->get_type() ) {
			return;
		}
		$wsv_exc_vari        = get_option( WSV_EXCEPT_SING_VARI );
		$wsv_exc_parent      = get_option( WSV_EXC_PROD_PAR );
		$wsv_exc_varia_table = get_option( WSV_EXC_PROD_TABLE );

		$wsv_exc_vari        = is_array( $wsv_exc_vari ) ? $wsv_exc_vari : array();
		$wsv_exc_parent      = is_array( $wsv_exc_parent ) ? $wsv_exc_parent : array();
		$wsv_exc_varia_table = is_array( $wsv_exc_varia_table ) ? $wsv_exc_varia_table : array();

		$vars_id = $product->get_children();
		if ( isset( $_POST[ WSV_EXCEPT_SING_VARI ] ) ) {
			$wsv_exc_vari[ $post_id ] = $vars_id;
			update_post_meta( $post_id, WSV_EXCEPT_SING_VARI, 'yes' );
		} else {
			foreach ( $vars_id as $var_id ) {
				$hide_variation = get_post_meta( $var_id, WSV_HIDE_VARIATION, true );
				if ( 'yes' !== $hide_variation ) {
					$wsv_exc_vari[ $post_id ] = array_unique( $wsv_exc_vari[ $post_id ] );

					$key = array_search( $var_id, $wsv_exc_vari[ $post_id ], false );
					if ( false !== $key ) {
						unset( $wsv_exc_vari[ $post_id ][ $key ] );
					}
				}
			}
			update_post_meta( $post_id, WSV_EXCEPT_SING_VARI, 'no' );
		}
		update_option( WSV_EXCEPT_SING_VARI, $wsv_exc_vari );

		if ( isset( $_POST[ WSV_EXC_PROD_PAR ] ) ) {
			$wsv_exc_parent[] = $post_id;
			update_post_meta( $post_id, WSV_EXC_PROD_PAR, 'yes' );
		} else {
			$key = array_search( $post_id, $wsv_exc_parent, true );
			if ( $key !== false ) {
				unset( $wsv_exc_parent[ $key ] );
			}
			update_post_meta( $post_id, WSV_EXC_PROD_PAR, 'no' );
		}
		$wsv_exc_parent = array_unique( $wsv_exc_parent );
		update_option( WSV_EXC_PROD_PAR, $wsv_exc_parent );

		if ( isset( $_POST[ WSV_EXC_PROD_TABLE ] ) ) {
			$wsv_exc_varia_table[] = $post_id;
			update_post_meta( $post_id, WSV_EXC_PROD_TABLE, 'yes' );
		} else {
			$key = array_search( $post_id, $wsv_exc_varia_table, true );
			if ( $key !== false ) {
				unset( $wsv_exc_varia_table[ $key ] );
			}
			update_post_meta( $post_id, WSV_EXC_PROD_TABLE, 'no' );
		}
		$wsv_exc_varia_table = array_unique( $wsv_exc_varia_table );
		update_option( WSV_EXC_PROD_TABLE, $wsv_exc_varia_table );
	}

	public function footer_credits( $text ) {
		$screen = get_current_screen();
		if ( 'woocommerce_page_wsv' === $screen->base ) {
			$text = sprintf( __( 'If you like %1$s please leave us a %2$s rating or some reviews.This will make happy %3$s.', 'woo-wsv' ), sprintf( '<strong>%s</strong>', WSV_PLUGIN_NAME ), '<a href="https://wordpress.org/support/plugin/show-product-variations-for-woocommerce/reviews/#new-post" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woo-wsv' ) . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>', sprintf( '<strong>%s</strong>', esc_html__( 'FLINM', 'woo-wsv' ) ) );
		}
		return $text;
	}

	public function add_custom_field_to_variations( $loop, $variation_data, $variation_post ) {
		woocommerce_wp_text_input(
			array(
				'id'            => 'wsv_custom_name[' . $variation_post->ID . ']',
				'name'          => 'wsv_custom_name[' . $variation_post->ID . ']',
				'label'         => __( 'Custom Name(' . WSV_PLUGIN_NAME . ' ) ', 'show-product-variations-for-woocommerce' ),
				'value'         => get_post_meta( $variation_post->ID, 'wsv_custom_name', true ),
				'desc_tip'      => true,
				'description'   => __( 'Add custom name for this variation', 'show-product-variations-for-woocommerce' ),
				'type'          => 'text',
				'wrapper_class' => 'form-row form-row-full',
			)
		);

		woocommerce_wp_checkbox(
			array( // Checkbox.
				'id'          => 'wsv_hide_variation[' . $variation_post->ID . ']',
				'name'        => 'wsv_hide_variation[' . $variation_post->ID . ']',
				'label'       => __( 'Hide this variation(' . WSV_PLUGIN_NAME . ' ) ', 'show-product-variations-for-woocommerce' ),
				'value'       => get_post_meta( $variation_post->ID, WSV_HIDE_VARIATION, true ),
				'description' => __( 'Enable this will hide variation on shop and categorie page.', 'woocommerce' ),
				'desc_tip'    => true,
			)
		);
	}

	public function save_custom_field_variations( $variation_id ) {
		$custom_field = $_POST['wsv_custom_name'][ $variation_id ];

		$hide_variation = $_POST[ WSV_HIDE_VARIATION ][ $variation_id ];

		$variation = wc_get_product( $variation_id );
		$parent_id = $variation->get_parent_id();

		$wsv_exc_vari   = get_option( WSV_EXCEPT_SING_VARI );
		$wsv_exc_parent = get_option( WSV_EXC_PROD_PAR );

		if ( ! in_array( $parent_id, $wsv_exc_parent, true ) ) {
			if ( 'yes' === $hide_variation ) {
				$wsv_exc_vari[ $parent_id ][] = $variation_id;
				$wsv_exc_vari[ $parent_id ]   = array_unique( $wsv_exc_vari[ $parent_id ] );
			} else {
				// if the variation is not chosen and its id is in the products to be excluded then it is removed from the table.
				if ( is_array( $wsv_exc_vari ) && array_key_exists( $parent_id, $wsv_exc_vari ) && is_array( $wsv_exc_vari[ $parent_id ] ) ) {
					$wsv_exc_vari[ $parent_id ] = array_unique( $wsv_exc_vari[ $parent_id ] );

					$key = array_search( $variation_id, $wsv_exc_vari[ $parent_id ], false );
					if ( false !== $key ) {
						unset( $wsv_exc_vari[ $parent_id ][ $key ] );
					}
				}
			}
			update_option( WSV_EXCEPT_SING_VARI, $wsv_exc_vari );
		}

		update_post_meta( $variation_id, 'wsv_custom_name', esc_attr( $custom_field ) );
		update_post_meta( $variation_id, WSV_HIDE_VARIATION, $hide_variation );
	}
}
