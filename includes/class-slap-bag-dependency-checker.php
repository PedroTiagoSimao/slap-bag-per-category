<?php
/**
 * Admin notices and dependency checks.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Dependency_Checker {

    /**
     * Initialize the class and set up hooks.
     *
     * @since    1.0.0
     */
    public function __construct() {
        add_action( 'admin_notices', array( $this, 'check_dependencies' ) );
    }

    /**
     * Check if required plugins are active and show notices if not.
     *
     * @since    1.0.0
     */
    public function check_dependencies() {
        $missing_plugins = array();

        // Check for WooCommerce
        if ( ! class_exists( 'WooCommerce' ) ) {
            $missing_plugins[] = 'WooCommerce';
        }

        // Check for Advanced Custom Fields
        if ( ! function_exists( 'get_field' ) ) {
            $missing_plugins[] = 'Advanced Custom Fields (ACF)';
        }

        // Display notice if any dependencies are missing
        if ( ! empty( $missing_plugins ) ) {
            $plugin_list = implode( ', ', $missing_plugins );
            ?>
            <div class="notice notice-error">
                <p>
                    <strong><?php _e( 'SLAP - Saco por restaurante', 'slap-bag-per-category' ); ?>:</strong>
                    <?php 
                    printf(
                        __( 'This plugin requires %s to be installed and activated.', 'slap-bag-per-category' ),
                        $plugin_list
                    ); 
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Check if all dependencies are met.
     *
     * @since    1.0.0
     * @return   bool    True if all dependencies are met, false otherwise.
     */
    public static function dependencies_met() {
        return class_exists( 'WooCommerce' ) && function_exists( 'get_field' );
    }
}
