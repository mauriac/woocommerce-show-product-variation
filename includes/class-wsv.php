<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       #
 * @since      1.0.0
 *
 * @package    Wsv
 * @subpackage Wsv/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wsv
 * @subpackage Wsv/includes
 * @author     Mauriac Azoua <azouzmauriac@gmail.com>
 */
class Wsv {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Wsv_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'WSV_VERSION' ) ) {
			$this->version = WSV_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'show-product-variations-for-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wsv_Loader. Orchestrates the hooks of the plugin.
	 * - Wsv_i18n. Defines internationalization functionality.
	 * - Wsv_Admin. Defines all hooks for the admin area.
	 * - Wsv_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wsv-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wsv-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wsv-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wsv-public.php';

		$this->loader = new Wsv_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wsv_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Wsv_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Wsv_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_wsv_menu' );
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'is_woocommerce_enabled' );

		$this->loader->add_filter( 'woocommerce_product_data_tabs', $plugin_admin, 'get_product_tab_label' );
		$this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'get_product_tab_data' );
		$this->loader->add_action( 'woocommerce_process_product_meta', $plugin_admin, 'save_post' );
		$this->loader->add_filter( 'admin_footer_text', $plugin_admin, 'footer_credits', 10, 1 );

		$this->loader->add_action( 'woocommerce_product_after_variable_attributes', $plugin_admin, 'add_custom_field_to_variations', 99, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'save_custom_field_variations', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Wsv_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_shortcode_products_query', $plugin_public, 'show_variations_by_shortcode' );
		// $this->loader->add_action( 'wp_ajax_wsv_add_product_to_cart', $plugin_public, 'add_product_to_cart' );
		// $this->loader->add_action( 'wp_ajax_nopriv_wsv_add_product_to_cart', $plugin_public, 'add_product_to_cart' );
		$this->loader->add_action( 'woocommerce_product_query', $plugin_public, 'product_query' );

		$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'display_variation_as_dropdown', 10, 2 );
		$this->loader->add_filter( 'woocommerce_product_variation_title', $plugin_public, 'display_product_title', 10, 2 );

		$this->loader->add_filter( 'woocommerce_variable_sale_price_html', $plugin_public, 'show_variation_price_format', 10, 2 );
		$this->loader->add_filter( 'woocommerce_variable_price_html', $plugin_public, 'show_variation_price_format', 10, 2 );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Wsv_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
