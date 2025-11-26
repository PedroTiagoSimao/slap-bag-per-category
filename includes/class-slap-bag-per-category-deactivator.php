<?php
/**
 * Fired during plugin deactivation.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        // Add deactivation code here
        // For example: clear scheduled events, flush cache, etc.
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
