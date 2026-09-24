# SLAP - Saco por restaurante

A WordPress plugin for managing bags per restaurant category.

## Description

This plugin provides functionality for managing bags per restaurant category in WordPress and WooCommerce. It automatically adds bag fees to the cart based on product categories, using ACF (Advanced Custom Fields) to store the fee values.

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- **WooCommerce** (required)
- **Advanced Custom Fields (ACF)** (required)

## Installation

1. Upload the plugin files to the `/wp-content/plugins/slap-bag-per-category` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Make sure WooCommerce and Advanced Custom Fields are installed and activated.
4. Create an ACF field called `valor_saco` in your product category taxonomy.

## How It Works

1. **ACF Field Setup**: Create a number field called `valor_saco` in your product categories (taxonomy: `product_cat`)
2. **Set Fee Values**: Enter the bag fee amount for each category that requires a bag fee
3. **Automatic Fee Addition**: When a customer adds products to their cart:
   - The plugin checks each product's categories
   - If a category has a `valor_saco` value set, a fee is added to the cart
   - The fee is named "Saco [Category Name]"
   - Each category fee is only added once, regardless of how many products from that category are in the cart
4. **Automatic Fee Removal**: When the cart is updated and no products from a category remain, the fee is automatically removed

## Features

- **Automatic Bag Fee Calculation**: Automatically adds bag fees based on product categories
- **ACF Integration**: Uses Advanced Custom Fields to store fee values per category
- **WooCommerce Compatible**: Seamlessly integrates with WooCommerce cart and checkout
- **One Fee Per Category**: Each category fee is added only once, regardless of quantity
- **Dynamic Fee Management**: Fees are automatically added/removed when cart is updated
- **Translatable**: Fee names support internationalization
- **Clean Architecture**: Object-oriented design following WordPress coding standards
- **Dependency Checking**: Alerts admins if required plugins are missing
- **Easy to Extend**: Well-documented code structure for customization

## Developer Information

- **Author:** Pedro Simão
- **Website:** [https://slap.pt](https://slap.pt)
- **Version:** 1.0.0
- **License:** GPL v2 or later

## File Structure

```
slap-bag-per-category/
├── admin/
│   ├── css/
│   │   └── slap-bag-per-category-admin.css
│   ├── js/
│   │   └── slap-bag-per-category-admin.js
│   └── class-slap-bag-per-category-admin.php
├── includes/
│   ├── class-slap-bag-per-category.php
│   ├── class-slap-bag-per-category-activator.php
│   ├── class-slap-bag-per-category-deactivator.php
│   ├── class-slap-bag-per-category-i18n.php
│   └── class-slap-bag-per-category-loader.php
├── languages/
├── public/
│   ├── css/
│   │   └── slap-bag-per-category-public.css
│   ├── js/
│   │   └── slap-bag-per-category-public.js
│   └── class-slap-bag-per-category-public.php
├── slap-bag-per-category.php
└── README.md
```

## Changelog

### 1.0.0

- Initial release

### 1.0.1

- Fixed a bug where the plugin was not calculating the correct fee when product quantity was greater than 1.

### 1.0.2

- Fixed `Embalagens` fee calculation so different variations of the same product are summed separately in the cart and checkout.

## Support

For support, please visit [https://slap.pt](https://slap.pt)
