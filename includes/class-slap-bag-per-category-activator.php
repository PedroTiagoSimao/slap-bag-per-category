<?php
/**
 * Fired during plugin activation.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category_Activator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        // Add activation code here
        // For example: create database tables, set default options, etc.
        
        // Set default options
        $default_options = array(
            'version' => SLAP_BAG_PER_CATEGORY_VERSION,
            'activated_time' => current_time( 'timestamp' )
        );
        
        add_option( 'slap_bag_per_category_options', $default_options );
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
