<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      SLAP_Bag_Per_Category_Loader    $loader    Maintains and registers all hooks for the plugin.
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
     * @since    1.0.0
     */
    public function __construct() {
        if ( defined( 'SLAP_BAG_PER_CATEGORY_VERSION' ) ) {
            $this->version = SLAP_BAG_PER_CATEGORY_VERSION;
        } else {
            $this->version = '1.0.2';
        }
        $this->plugin_name = 'slap-bag-per-category';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->init_fee_manager();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-per-category-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-per-category-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'admin/class-slap-bag-per-category-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'public/class-slap-bag-per-category-public.php';

        /**
         * The class responsible for managing bag fees per category.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-fee-manager.php';

        /**
         * The class responsible for checking plugin dependencies.
         */
        require_once SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'includes/class-slap-bag-dependency-checker.php';

        $this->loader = new SLAP_Bag_Per_Category_Loader();
        
        // Initialize dependency checker
        new SLAP_Bag_Dependency_Checker();
    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {
        $plugin_i18n = new SLAP_Bag_Per_Category_i18n();

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
        $plugin_admin = new SLAP_Bag_Per_Category_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_settings_page' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {
        $plugin_public = new SLAP_Bag_Per_Category_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 99 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        // Embalagem info removed from product page
    }

    /**
     * Initialize the bag fee manager.
     *
     * @since    1.0.0
     * @access   private
     */
    private function init_fee_manager() {
        // Initialize the fee manager (it will check for WooCommerce internally)
        new SLAP_Bag_Fee_Manager();
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
     * @return    SLAP_Bag_Per_Category_Loader    Orchestrates the hooks of the plugin.
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
