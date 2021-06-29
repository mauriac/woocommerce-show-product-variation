<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              #
 * @since             1.0.0
 * @package           Wsv
 *
 * @wordpress-plugin
 * Plugin Name:       Show Variations For Woocommerce
 * Plugin URI:        #
 * Description:       Display variations as single product or show variations dropdown on shop and category page; show table of all available variations of a variable product, and more.
 * Version:           1.6
 * Author:            FLINIMI
 * Author URI:        azouamauriac@gmail.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wsv
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WSV_VERSION', '1.6' );
define( 'WSV_PLUGIN_NAME', 'Show Product Variations For Woocommerce' );
/**
 * Designates the label of variation that will be excluded.
 */
define( 'WSV_EXCEPT_SING_VARI', 'wsv_except_single_variation' );
/**
 * Designates the label variable product(the parent) that will be excluded.
 */
define( 'WSV_EXC_PROD_PAR', 'wsv_exclude_product_parent' );
/**
 * Designates the label of variation that user have selected and will be excluded.
 */
define( 'WSV_HIDE_VARIATION', 'wsv_hide_variation' );

define( 'WSV_EXC_PROD_TABLE', 'wsv_exclude_product_table' );
define( 'WSV_REVIEWS', 'https://wordpress.org/plugins/show-product-variations-for-woocommerce/#reviews' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wsv-activator.php
 */
function activate_wsv() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wsv-activator.php';
	Wsv_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wsv-deactivator.php
 */
function deactivate_wsv() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wsv-deactivator.php';
	Wsv_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wsv' );
register_deactivation_hook( __FILE__, 'deactivate_wsv' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/functions.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-wsv.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wsv() {

	$plugin = new Wsv();
	$plugin->run();

}
run_wsv();
