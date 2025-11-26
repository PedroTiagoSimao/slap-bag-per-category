<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category_i18n {

    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain(
            'slap-bag-per-category',
            false,
            dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
        );
    }
}
