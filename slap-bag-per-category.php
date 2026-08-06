<?php
/**
 * Plugin Name: SLAP - Sacos e Embalagens
 * Plugin URI: https://slap.pt
 * Description: SLAP - Gestão de sacos e embalagens por restaurante e produto.
 * Version: 1.0.1
 * Author: Pedro Simão
 * Author URI: https://slap.pt
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: slap-bag-per-category
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * Currently plugin version.
 */
define( 'SLAP_BAG_PER_CATEGORY_VERSION', '1.0.0' );
define( 'SLAP_BAG_PER_CATEGORY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SLAP_BAG_PER_CATEGORY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SLAP_BAG_PER_CATEGORY_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_slap_bag_per_category() {
    require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-per-category-activator.php';
    SLAP_Bag_Per_Category_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_slap_bag_per_category() {
    require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-per-category-deactivator.php';
    SLAP_Bag_Per_Category_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_slap_bag_per_category' );
register_deactivation_hook( __FILE__, 'deactivate_slap_bag_per_category' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-per-category.php';

/**
 * Begins execution of the plugin.
 */
function run_slap_bag_per_category() {
    $plugin = new SLAP_Bag_Per_Category();
    $plugin->run();
}

run_slap_bag_per_category();

// Fee Manager is initialized inside the core class constructor
