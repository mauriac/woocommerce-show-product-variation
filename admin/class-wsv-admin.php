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
		echo '<div class="error"><p><strong>' . esc_attr__( WSV_PLUGIN_NAME, 'wsv' ) . '</strong> ' . sprintf(__('requires %sWooCommerce%s to be installed & activated!', 'wsv'), '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>') . '</p></div>';
	}

	public static function get_settings_page() {
		if ( ( isset( $_POST['wsv-settings'], $_POST['securite_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['securite_nonce'] ), 'wsv_securite' ) ) ) {
			if ( ! isset( $_POST['wsv-settings']['wsv_enable_var_table_show'] ) ) {
				$_POST['wsv-settings']['wsv_enable_var_table_show'] = null;
			}
			wsv_update_option( wp_unslash( $_POST['wsv-settings'] ) );

			?>
			<div class="wad notice notice-success is-dismissible">
				<p>
					<?php
						echo '<b>' . esc_attr__( WSV_PLUGIN_NAME, 'wsv' ) . '</b>' . sprintf( __( ': Data saved successful!', 'wsv' ) );
					?>
				</p>
			</div>
			<?php
		}
		$wsv_enable_var_table_show = get_option( 'wsv_enable_var_table_show' );
		$wsv_show_vari_on_shop_cat = get_option( 'wsv_show_vari_on_shop_cat' );
		?>
			<h1 style="font-size: 23px; text-transform: uppercase; margin: 1em 0;"><?php _e( 'Wc Show Variation Settings', 'wsv' ); ?></h1>
			<form method="POST">

			<table>
				<tr>
					<th scope="row">
						<strong>
							<label class="form-check-label" for="wsv_enable_var_table_show"><?php _e( 'Enable Variations Table', 'wsv' ); ?></label>
						</strong>
					</th>
					<td>
						<input type="checkbox" 
						<?php
							echo ( ( $wsv_enable_var_table_show ) ? 'checked' : '' );
						?>
						name="wsv-settings[wsv_enable_var_table_show]" class="form-check-input" id="wsv_enable_var_table_show" /><br />
					</td>
				</tr>
				<tr>
					<div class="col-auto my-1">
						<th scope="row">
							<strong>
								<?php esc_attr_e( 'Show Variations On Shop & Category As', 'wsv' ); ?>
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
									<?php esc_attr_e( 'Single Product', 'wsv' ); ?>
								</option>
								<option value="dp" 
									<?php
										echo ( ( 'dp' === $wsv_show_vari_on_shop_cat ) ? 'selected' : '' );
									?>
									>
									<?php esc_attr_e( 'Dropdown', 'wsv' ); ?>
								</option>
							</select>
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

	public function add_wsv_menu() {
		add_options_page( 'Wc Show Variation Settings', 'Wc Show Variation Settings', 'manage_options', 'wsv', array( $this, 'get_settings_page' ) );
	}

	public function get_product_tab_data() {
		?>
		<div id='wsv_tab_content' class='panel woocommerce_options_panel'>
			<div class='options_group'>
				<?php
					woocommerce_wp_checkbox(
						array(
							'id'          => WSV_EXCEPT_SING_VARI,
							'label'       => __( 'Exclude Single Variation', 'wsv' ),
							'description' => __( 'This option will exclude single variation on shop & category pages.', 'wsv' ),
						)
					);
					woocommerce_wp_checkbox(
						array(
							'id'          => WSV_EXC_PROD_PAR,
							'label'       => __( 'Hide Parent Variable Product', 'wsv' ),
							'description' => __( 'Enable this option to Hide parent variation on shop & category pages.<br/><strong>Note: this option will be not work for Show Variations Dropdown</strong>', 'wsv' ),
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
			'label'  => __( 'Show Variations', 'wsv' ),
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
		$wsv_exc_vari   = get_option( WSV_EXCEPT_SING_VARI );
		$wsv_exc_parent = get_option( WSV_EXC_PROD_PAR );
		$wsv_exc_vari   = is_array( $wsv_exc_vari ) ? $wsv_exc_vari : array();
		$wsv_exc_parent = is_array( $wsv_exc_parent ) ? $wsv_exc_parent : array();

		if ( isset( $_POST[ WSV_EXCEPT_SING_VARI ] ) ) {
			$vars_id                  = $product->get_children();
			$wsv_exc_vari[ $post_id ] = $vars_id;
			update_post_meta( $post_id, WSV_EXCEPT_SING_VARI, 'yes' );
		} else {
			$wsv_exc_vari[ $post_id ] = array();
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
	}
}
