<?php
/**
 * Manages bag fees per product category.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/includes
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Fee_Manager {

    /**
     * Initialize the class and set up hooks.
     *
     * @since    1.0.0
     */
    public function __construct() {
        // Add fees to cart
        add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_category_bag_fees' ), 10, 1 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'add_embalagem_item_data' ), 10, 2 );
        
        // Add debug info to cart page
        //add_action( 'woocommerce_before_cart', array( $this, 'display_debug_info' ) );
    }

    /**
     * Add bag fees based on product categories in cart.
     *
     * @since    1.0.0
     * @param    WC_Cart    $cart    The WooCommerce cart object.
     */
    public function add_category_bag_fees( $cart ) {
        // Don't add fees in admin or during AJAX requests (except cart updates)
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
            return;
        }

        // Check if WooCommerce and ACF are active
        if ( ! function_exists( 'get_field' ) || ! class_exists( 'WooCommerce' ) ) {
            return;
        }


        // Array to track which categories already processed and total fee
        $categories_with_fees = array();
        $total_bag_fee = 0;

        // Loop through cart items
        foreach ( $cart->get_cart() as $cart_item ) {
            $product_id = $cart_item['product_id'];
            $product_categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'ids' ) );

            if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) {
                foreach ( $product_categories as $category_id ) {
                    if ( in_array( $category_id, $categories_with_fees ) ) {
                        continue;
                    }

                    $bag_fee = get_field( 'valor_saco', 'product_cat_' . $category_id );

                    if ( $bag_fee && is_numeric( $bag_fee ) ) {
                        $fee_value = floatval( $bag_fee );
                        if ( $fee_value > 0 ) {
                            $total_bag_fee += $fee_value;
                            $categories_with_fees[] = $category_id;
                        }
                    }
                }
            }
        }

        if ( $total_bag_fee > 0 ) {
            $fee_name = __( 'Sacos', 'slap-bag-per-category' );
            $cart->add_fee( $fee_name, $total_bag_fee, false );
        }

        $processed_products = array();
        $total_embalagens_fee = 0;

        foreach ( $cart->get_cart() as $cart_item ) {
            $product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
            $product_id = $product ? $product->get_id() : ( isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0 );

            if ( ! $product_id ) {
                continue;
            }

            $embalagem = function_exists( 'get_field' ) ? get_field( 'embalagem', $product_id ) : null;
            $dedupe_id = $product_id;

            if ( ( ! $embalagem || $embalagem === '' ) && $product && $product->get_parent_id() ) {
                $parent_id = $product->get_parent_id();
                if ( $parent_id ) {
                    $embalagem_parent = function_exists( 'get_field' ) ? get_field( 'embalagem', $parent_id ) : null;
                    if ( $embalagem_parent ) {
                        $embalagem = $embalagem_parent;
                        $dedupe_id = $parent_id;
                    }
                }
            }

            if ( in_array( $dedupe_id, $processed_products ) ) {
                continue;
            }

            $fee_val = 0;
            if ( is_array( $embalagem ) ) {
                foreach ( $embalagem as $val ) {
                    if ( is_numeric( $val ) ) {
                        $num = floatval( $val );
                        if ( $num > 0 ) {
                            $fee_val += $num;
                        }
                    }
                }
            } else {
                if ( is_numeric( $embalagem ) ) {
                    $num = floatval( $embalagem );
                    if ( $num > 0 ) {
                        $fee_val += $num;
                    }
                }
            }

            if ( $fee_val > 0 ) {
                $total_embalagens_fee += $fee_val;
                $processed_products[] = $dedupe_id;
            }
        }

        if ( $total_embalagens_fee > 0 ) {
            $cart->add_fee( __( 'Embalagens', 'slap-bag-per-category' ), $total_embalagens_fee, false );
        }
    }

    public function add_embalagem_item_data( $item_data, $cart_item ) {
        if ( ! function_exists( 'get_field' ) ) {
            return $item_data;
        }

        foreach ( $item_data as $existing ) {
            if ( isset( $existing['name'] ) && $existing['name'] === __( 'Embalagem', 'slap-bag-per-category' ) ) {
                return $item_data;
            }
        }

        $product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
        $product_id = $product ? $product->get_id() : ( isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0 );

        if ( ! $product_id ) {
            return $item_data;
        }

        $embalagem = get_field( 'embalagem', $product_id );

        if ( ( ! $embalagem || $embalagem === '' ) && $product && $product->get_parent_id() ) {
            $parent_id = $product->get_parent_id();
            if ( $parent_id ) {
                $embalagem = get_field( 'embalagem', $parent_id );
            }
        }

        if ( $embalagem ) {
            if ( is_array( $embalagem ) ) {
                $formatted = array();
                foreach ( $embalagem as $val ) {
                    if ( is_numeric( $val ) ) {
                        $formatted[] = number_format( floatval( $val ), 2, ',', '.' ) . '€';
                    } else {
                        $formatted[] = (string) $val;
                    }
                }
                $display_value = implode( ', ', $formatted );
            } else {
                if ( is_numeric( $embalagem ) ) {
                    $display_value = number_format( floatval( $embalagem ), 2, ',', '.' ) . '€';
                } else {
                    $display_value = (string) $embalagem;
                }
            }
            $item_data[] = array(
                'name'  => __( 'Embalagem', 'slap-bag-per-category' ),
                'value' => wp_kses_post( $display_value ),
                'display' => wp_kses_post( $display_value ),
            );
        }

        return $item_data;
    }

    /**
     * Display debug information on the cart page.
     *
     * @since    1.0.0
     */
    public function display_debug_info() {
        // Only show to administrators
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
            echo '<div class="woocommerce-info" style="background: #f0f0f0; border-left: 4px solid #ff0000;">';
            echo '<strong>DEBUG:</strong> WooCommerce cart not available.';
            echo '</div>';
            return;
        }

        $debug_info = array();
        $debug_info['acf_active'] = function_exists( 'get_field' );
        $debug_info['woocommerce_active'] = class_exists( 'WooCommerce' );
        $debug_info['cart_items'] = array();

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            $product_id = $cart_item['product_id'];
            $product = $cart_item['data'];
            
            $item_debug = array();
            $item_debug['product_id'] = $product_id;
            $item_debug['product_name'] = $product->get_name();
            $item_debug['categories'] = array();

            // Get product categories
            $product_categories = wp_get_post_terms( $product_id, 'product_cat', array( 'fields' => 'all' ) );

            if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ) {
                foreach ( $product_categories as $category ) {
                    $cat_info = array();
                    $cat_info['id'] = $category->term_id;
                    $cat_info['name'] = $category->name;
                    $cat_info['slug'] = $category->slug;
                    
                    // Try to get ACF field value
                    $acf_key = 'product_cat_' . $category->term_id;
                    $bag_fee = get_field( 'valor_saco', $acf_key );
                    
                    $cat_info['acf_key'] = $acf_key;
                    $cat_info['valor_saco_raw'] = $bag_fee;
                    $cat_info['valor_saco_type'] = gettype( $bag_fee );
                    $cat_info['valor_saco_float'] = $bag_fee ? floatval( $bag_fee ) : 0;
                    $cat_info['is_numeric'] = is_numeric( $bag_fee );
                    $cat_info['is_greater_than_zero'] = $bag_fee && is_numeric( $bag_fee ) && floatval( $bag_fee ) > 0;
                    
                    // Also try alternative ACF retrieval methods
                    $cat_info['get_field_alternative'] = get_field( 'valor_saco', 'term_' . $category->term_id );
                    
                    $item_debug['categories'][] = $cat_info;
                }
            } else {
                $item_debug['categories_error'] = is_wp_error( $product_categories ) ? $product_categories->get_error_message() : 'No categories found';
            }

            $debug_info['cart_items'][] = $item_debug;
        }

        // Display debug information
        echo '<div class="woocommerce-info" style="background: #f9f9f9; border-left: 4px solid #2271b1; padding: 15px; margin-bottom: 20px; font-family: monospace; font-size: 12px;">';
        echo '<h3 style="margin-top: 0; font-family: -apple-system, BlinkMacSystemFont, sans-serif;">🔍 SLAP Debug Information (Admin Only)</h3>';
        
        echo '<p><strong>Plugin Status:</strong></p>';
        echo '<ul>';
        echo '<li>ACF Active: ' . ( $debug_info['acf_active'] ? '✅ Yes' : '❌ No' ) . '</li>';
        echo '<li>WooCommerce Active: ' . ( $debug_info['woocommerce_active'] ? '✅ Yes' : '❌ No' ) . '</li>';
        echo '</ul>';

        if ( empty( $debug_info['cart_items'] ) ) {
            echo '<p><strong>⚠️ No items in cart</strong></p>';
        } else {
            echo '<p><strong>Cart Items Analysis:</strong></p>';
            foreach ( $debug_info['cart_items'] as $index => $item ) {
                echo '<div style="background: white; padding: 10px; margin: 10px 0; border: 1px solid #ddd;">';
                echo '<strong>Product #' . ($index + 1) . ':</strong> ' . esc_html( $item['product_name'] ) . ' (ID: ' . $item['product_id'] . ')<br>';
                
                if ( ! empty( $item['categories'] ) ) {
                    echo '<strong>Categories:</strong><br>';
                    foreach ( $item['categories'] as $cat ) {
                        echo '<div style="margin-left: 20px; padding: 5px; background: #f0f0f0; margin-top: 5px;">';
                        echo '📁 <strong>' . esc_html( $cat['name'] ) . '</strong> (ID: ' . $cat['id'] . ')<br>';
                        echo '&nbsp;&nbsp;&nbsp;ACF Key: <code>' . esc_html( $cat['acf_key'] ) . '</code><br>';
                        echo '&nbsp;&nbsp;&nbsp;valor_saco (raw): <code>' . var_export( $cat['valor_saco_raw'], true ) . '</code><br>';
                        echo '&nbsp;&nbsp;&nbsp;Type: <code>' . $cat['valor_saco_type'] . '</code><br>';
                        echo '&nbsp;&nbsp;&nbsp;Float Value: <code>' . $cat['valor_saco_float'] . '</code><br>';
                        echo '&nbsp;&nbsp;&nbsp;Is Numeric: ' . ( $cat['is_numeric'] ? '✅ Yes' : '❌ No' ) . '<br>';
                        echo '&nbsp;&nbsp;&nbsp;Is > 0: ' . ( $cat['is_greater_than_zero'] ? '✅ Yes' : '❌ No' ) . '<br>';
                        
                        if ( $cat['get_field_alternative'] !== $cat['valor_saco_raw'] ) {
                            echo '&nbsp;&nbsp;&nbsp;Alternative method: <code>' . var_export( $cat['get_field_alternative'], true ) . '</code><br>';
                        }
                        
                        if ( $cat['is_greater_than_zero'] ) {
                            echo '&nbsp;&nbsp;&nbsp;<strong style="color: green;">✅ Fee should be added: €' . number_format( $cat['valor_saco_float'], 2 ) . '</strong><br>';
                        } else {
                            echo '&nbsp;&nbsp;&nbsp;<strong style="color: red;">❌ Fee will NOT be added</strong><br>';
                            if ( ! $cat['valor_saco_raw'] ) {
                                echo '&nbsp;&nbsp;&nbsp;<em>Reason: No value set in ACF field</em><br>';
                            } elseif ( ! $cat['is_numeric'] ) {
                                echo '&nbsp;&nbsp;&nbsp;<em>Reason: Value is not numeric</em><br>';
                            } elseif ( $cat['valor_saco_float'] <= 0 ) {
                                echo '&nbsp;&nbsp;&nbsp;<em>Reason: Value is not greater than 0</em><br>';
                            }
                        }
                        echo '</div>';
                    }
                } else {
                    echo '<strong style="color: red;">⚠️ No categories found</strong><br>';
                    if ( isset( $item['categories_error'] ) ) {
                        echo '<em>Error: ' . esc_html( $item['categories_error'] ) . '</em><br>';
                    }
                }
                echo '</div>';
            }
        }

        echo '<p style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd;"><em>This debug information is only visible to administrators. To disable, comment out the debug hook in class-slap-bag-fee-manager.php</em></p>';
        echo '</div>';
    }
}
