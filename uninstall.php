<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete plugin options
 */
delete_option( 'slap_bag_per_category_options' );

/**
 * For multisite installations
 */
if ( is_multisite() ) {
	global $wpdb;
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	
	foreach ( $blog_ids as $blog_id ) {
		switch_to_blog( $blog_id );
		delete_option( 'slap_bag_per_category_options' );
		restore_current_blog();
	}
}

/**
 * Drop custom database tables if any
 * Example:
 * global $wpdb;
 * $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}slap_custom_table" );
 */
