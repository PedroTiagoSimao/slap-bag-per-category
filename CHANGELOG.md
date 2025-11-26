# Changelog

All notable changes to the SLAP - Saco por restaurante plugin will be documented in this file.

## [1.0.0] - 2025-11-26

### Added
- Initial plugin release
- WordPress plugin boilerplate structure
- ACF integration for category-based bag fees
- WooCommerce cart fee management
- Automatic fee calculation based on product categories
- Dynamic fee addition/removal on cart updates
- Dependency checker for WooCommerce and ACF
- Admin notices for missing dependencies
- Internationalization support
- Comprehensive documentation (README.md and ACF-SETUP-GUIDE.md)

### Features
- One fee per category regardless of product quantity
- Automatic fee naming: "Saco [Category Name]"
- Clean, object-oriented architecture
- Follows WordPress coding standards
- Easy to extend and customize

### Requirements
- WordPress 5.8+
- PHP 7.4+
- WooCommerce (required)
- Advanced Custom Fields (required)

---

## Future Enhancements (Planned)

- [ ] Admin settings page for global configuration
- [ ] Option to customize fee name format
- [ ] Support for multiple fee types per category
- [ ] Fee exemption rules based on cart total
- [ ] Detailed fee breakdown in cart/checkout
- [ ] Export/import fee settings
- [ ] Analytics and reporting for bag fees collected
