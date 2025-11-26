# Plugin Loading Test

To verify the plugin is loading correctly, add this temporary code to your theme's `functions.php`:

```php
// TEMPORARY TEST - Remove after testing
add_action( 'wp_footer', function() {
    if ( current_user_can( 'manage_options' ) ) {
        echo '<!-- SLAP Plugin Test -->';
        echo '<div style="position: fixed; bottom: 0; right: 0; background: #000; color: #fff; padding: 10px; z-index: 9999;">';
        echo 'SLAP Plugin Loaded: ' . ( class_exists( 'SLAP_Bag_Fee_Manager' ) ? 'YES ✅' : 'NO ❌' );
        echo '</div>';
    }
});
```

This will show a small box in the bottom-right corner of your site (admin only) indicating if the plugin class is loaded.

## If the plugin is NOT loading:

1. **Check if plugin is activated**:
   - Go to Plugins page
   - Make sure "SLAP - Saco por restaurante" is activated

2. **Check for PHP errors**:
   - Enable WordPress debug mode in `wp-config.php`:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   define( 'WP_DEBUG_DISPLAY', false );
   ```
   - Check `/wp-content/debug.log` for errors

3. **Verify file structure**:
   - Make sure all plugin files are in the correct location
   - Path should be: `/wp-content/plugins/slap-bag-per-category/`

4. **Try deactivating and reactivating**:
   - Go to Plugins
   - Deactivate the plugin
   - Reactivate it

## If the plugin IS loading but debug info not showing:

The issue is with the hook. The debug info should now appear on the cart page.
