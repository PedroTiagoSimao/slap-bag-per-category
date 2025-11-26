# Troubleshooting Guide

## Debug Information

The plugin now displays comprehensive debug information on the cart page **for administrators only**.

### How to View Debug Info

1. Make sure you're logged in as an **Administrator**
2. Go to the **Cart page** (with items in cart)
3. Look for the debug panel at the top of the cart (before the cart table)

### What the Debug Info Shows

The debug panel displays:

- ✅ **Plugin Status**: Whether ACF and WooCommerce are active
- 📦 **Cart Items**: All products currently in the cart
- 📁 **Categories**: All categories for each product
- 🔍 **ACF Field Values**: The exact value stored in `valor_saco` for each category
- ✅/❌ **Fee Status**: Whether the fee will be added and why/why not

### Common Issues and Solutions

#### Issue 1: "No value set in ACF field"
**Problem**: The `valor_saco` field is empty for the category.

**Solution**:
1. Go to **Products > Categories**
2. Edit the category
3. Enter a value in the "Valor do Saco" field
4. Update the category

#### Issue 2: "Value is not numeric"
**Problem**: The ACF field contains non-numeric data.

**Solution**:
1. Make sure the ACF field type is set to **Number**
2. Check that you're entering only numbers (e.g., `0.10`, not `€0.10`)
3. Re-save the category with a numeric value

#### Issue 3: "Value is not greater than 0"
**Problem**: The value is 0 or negative.

**Solution**:
- Enter a positive number greater than 0 (e.g., `0.10` for 10 cents)

#### Issue 4: ACF field not showing
**Problem**: The ACF field doesn't appear when editing categories.

**Solution**:
1. Go to **Custom Fields > Field Groups**
2. Find your field group
3. Check **Location Rules** - should be set to "Taxonomy Term is equal to Product Category"
4. Make sure the field name is exactly `valor_saco`

#### Issue 5: Fee not appearing in cart
**Problem**: Debug shows "Fee should be added" but it's not in the cart.

**Possible causes**:
1. **Cache issue**: Clear your browser cache and WooCommerce cache
2. **Theme conflict**: Try switching to a default WordPress theme temporarily
3. **Plugin conflict**: Deactivate other plugins one by one to find conflicts
4. **Hook priority**: Another plugin might be interfering with cart fees

**Solutions**:
```php
// Try clearing WooCommerce transients
// Add this to functions.php temporarily, then remove it
add_action( 'init', function() {
    WC_Cache_Helper::get_transient_version( 'shipping', true );
});
```

#### Issue 6: Wrong ACF key format
**Problem**: The debug shows the ACF key but no value is retrieved.

**Solution**:
The plugin tries two formats:
- `product_cat_{category_id}` (default)
- `term_{category_id}` (alternative)

Check which one works in your debug output. If neither works:
1. Verify ACF is properly installed
2. Check that the field is assigned to the Product Category taxonomy
3. Try re-creating the ACF field group

### Testing Checklist

- [ ] ACF plugin is active
- [ ] WooCommerce plugin is active
- [ ] ACF field `valor_saco` is created
- [ ] ACF field is assigned to Product Category taxonomy
- [ ] ACF field type is "Number"
- [ ] Category has a value set in `valor_saco` field
- [ ] Value is numeric and greater than 0
- [ ] Product is assigned to the category
- [ ] Product is added to cart
- [ ] Cart page is refreshed

### Disabling Debug Mode

Once you've resolved the issue, you can disable debug output:

1. Open `/includes/class-slap-bag-fee-manager.php`
2. Find line ~22: `add_action( 'woocommerce_before_cart', array( $this, 'display_debug_info' ) );`
3. Comment it out:
```php
// add_action( 'woocommerce_before_cart', array( $this, 'display_debug_info' ) );
```

### Still Having Issues?

If the debug information shows everything is correct but fees still aren't being added:

1. **Check WordPress debug log**:
   - Enable `WP_DEBUG` and `WP_DEBUG_LOG` in `wp-config.php`
   - Check `/wp-content/debug.log` for errors

2. **Check browser console**:
   - Open browser developer tools (F12)
   - Look for JavaScript errors that might prevent cart updates

3. **Test with default theme**:
   - Switch to Twenty Twenty-Four or another default theme
   - If it works, there's a theme conflict

4. **Check WooCommerce settings**:
   - Go to **WooCommerce > Settings > Tax**
   - Make sure tax settings aren't conflicting with fees

### Contact Support

If you still need help, provide:
- Screenshot of the debug panel
- WordPress version
- WooCommerce version
- ACF version
- Active theme name
- List of active plugins
