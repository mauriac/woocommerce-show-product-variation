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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

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

	public static function get_settings_page() {
		if ( ( isset( $_POST, $_POST['securite_nonce'] ) && wp_verify_nonce( sanitize_key( $_POST['securite_nonce'] ), 'wsv_securite' ) ) ) {
			wsv_update_option( $_POST );
		}
		$wsv_settings = get_option( 'm-wsv-settings');
		?>
			<h1 style="font-size: 23px; text-transform: uppercase; margin: 1em 0;"><?php _e( 'Wc Show Variation Settings', 'wsv' ); ?></h1>
			<form method="POST">
				<div class="col-md-2 form-check">
					<strong>
						<label class="form-check-label" for="wsv_enable_var_table_show"><?php _e( 'Enable Variations Table', 'wsv' ); ?></label>
					</strong>
					<input type="checkbox" <?php 
					if ( $wsv_settings && array_key_exists( 'wsv_enable_var_table_show', $wsv_settings ) ) {
						echo ( ( 'yes' === $wsv_settings['wsv_enable_var_table_show'] ) ? 'checked' : '' );
					}
					?>
					name="wsv_enable_var_table_show" class="form-check-input" value="yes" id="wsv_enable_var_table_show" /><br />
					<small class="form-text text-muted">
						<?php //_e( 'this option will display the list of variations of a variable product on that product\'s page', 'wsv' ); ?>
					</small>
				</div>
				<input type="hidden" name="securite_nonce" value="<?php echo esc_html( wp_create_nonce( 'wsv_securite' ) ); ?>"/>
				<span ><?php  submit_button(); ?></span>
			</form>
		</div>
		<?php
	}

	public function add_wsv_menu() {
		add_options_page( 'Wc Show Variation Settings', 'Wc Show Variation Settings', 'manage_options', 'wsv', array( $this, 'get_settings_page' ) );
	}
}
