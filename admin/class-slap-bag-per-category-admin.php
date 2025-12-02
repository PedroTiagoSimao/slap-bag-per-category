<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/admin
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private function normalize_pt_decimal( $val ) {
        $s = trim( (string) $val );
        if ( $s === '' ) {
            return '';
        }
        $s = str_replace( array( ' ', '€' ), '', $s );
        if ( strpos( $s, ',' ) !== false && strpos( $s, '.' ) !== false ) {
            $s = str_replace( '.', '', $s );
            $s = str_replace( ',', '.', $s );
        } elseif ( strpos( $s, ',' ) !== false ) {
            $s = str_replace( ',', '.', $s );
        }
        return floatval( $s );
    }

    private function format_pt_decimal( $num ) {
        if ( $num === '' || $num === null ) {
            return '';
        }
        return number_format( floatval( $num ), 2, ',', '.' );
    }

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in SLAP_Bag_Per_Category_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The SLAP_Bag_Per_Category_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style( 
            $this->plugin_name, 
            SLAP_BAG_PER_CATEGORY_PLUGIN_URL . 'admin/css/slap-bag-per-category-admin.css', 
            array(), 
            $this->version, 
            'all' 
        );
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in SLAP_Bag_Per_Category_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The SLAP_Bag_Per_Category_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script( 
            $this->plugin_name, 
            SLAP_BAG_PER_CATEGORY_PLUGIN_URL . 'admin/js/slap-bag-per-category-admin.js', 
            array( 'jquery' ), 
            $this->version, 
            false 
        );
    }

    public function register_settings_page() {
        add_submenu_page(
            'slap-settings',
            __( 'Sacos e Embalagens', 'slap-bag-per-category' ),
            __( 'Sacos e Embalagens', 'slap-bag-per-category' ),
            'manage_options',
            'slap-bag-default-valor-saco',
            array( $this, 'render_settings_page' )
        );
    }

    public function render_settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
        $updated_message = '';
        if ( isset( $_POST['slap_bag_defaults_nonce'] ) && wp_verify_nonce( $_POST['slap_bag_defaults_nonce'], 'slap_bag_defaults_action' ) ) {
            if ( isset( $_POST['default_valor_saco'] ) ) {
                $val = sanitize_text_field( $_POST['default_valor_saco'] );
                $normalized = $this->normalize_pt_decimal( $val );
                update_option( 'slap_bag_default_valor_saco', $normalized );
                $updated_message = __( 'Valor padrão guardado.', 'slap-bag-per-category' );
            }
            if ( isset( $_POST['apply_default_to_terms'] ) ) {
                $count = 0;
                $default = get_option( 'slap_bag_default_valor_saco' );
                $default_num = $this->normalize_pt_decimal( $default );
                if ( function_exists( 'get_field' ) && function_exists( 'update_field' ) && $default_num !== '' ) {
                    $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
                    if ( ! is_wp_error( $terms ) ) {
                        foreach ( $terms as $term ) {
                            $key = 'product_cat_' . $term->term_id;
                            $is_rest = get_field( 'e_restaurante', $key );
                            $current = get_field( 'valor_saco', $key );
                            $is_set = $current !== null && $current !== '' && is_numeric( $current ) && floatval( $current ) > 0;
                            if ( $is_rest && ! $is_set ) {
                                update_field( 'valor_saco', $default_num, $key );
                                $count++;
                            }
                        }
                    }
                }
                $updated_message = sprintf( __( 'Aplicado a %d restaurantes sem valor.', 'slap-bag-per-category' ), $count );
            }
            if ( isset( $_POST['clear_restaurant_values'] ) ) {
                $count = 0;
                if ( function_exists( 'get_field' ) && function_exists( 'update_field' ) ) {
                    $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
                    if ( ! is_wp_error( $terms ) ) {
                        foreach ( $terms as $term ) {
                            $key = 'product_cat_' . $term->term_id;
                            $is_rest = get_field( 'e_restaurante', $key );
                            if ( $is_rest ) {
                                update_field( 'valor_saco', '', $key );
                                $count++;
                            }
                        }
                    }
                }
                $updated_message = sprintf( __( 'Limpo o valor do saco em %d restaurantes.', 'slap-bag-per-category' ), $count );
            }
        }
        $current_default = get_option( 'slap_bag_default_valor_saco', '' );
        $current_default_display = $this->format_pt_decimal( $current_default );
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'Sacos e Embalagens', 'slap-bag-per-category' ) . '</h1>';
        if ( $updated_message ) {
            echo '<div class="updated notice"><p>' . esc_html( $updated_message ) . '</p></div>';
        }
        echo '<form method="post">';
        wp_nonce_field( 'slap_bag_defaults_action', 'slap_bag_defaults_nonce' );
        echo '<table class="form-table">';
        echo '<tr><th scope="row">' . esc_html__( 'Valor padrão do saco (€)', 'slap-bag-per-category' ) . '</th>';
        echo '<td><input type="text" name="default_valor_saco" value="' . esc_attr( $current_default_display ) . '" class="regular-text" /></td></tr>';
        echo '</table>';
        echo '<p class="submit"><button type="submit" class="button button-primary">' . esc_html__( 'Guardar', 'slap-bag-per-category' ) . '</button></p>';
        echo '<hr />';
        echo '<p><button type="submit" name="apply_default_to_terms" value="1" class="button">' . esc_html__( 'Aplicar valor padrão a todas as categorias de restaurante sem valor definido', 'slap-bag-per-category' ) . '</button></p>';
        echo '<p><button type="submit" name="clear_restaurant_values" value="1" class="button button-secondary">' . esc_html__( 'Limpar valor do saco em todos os restaurantes', 'slap-bag-per-category' ) . '</button></p>';
        echo '</form>';
        $restaurant_rows = array();
        $terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
        if ( ! is_wp_error( $terms ) ) {
            foreach ( $terms as $term ) {
                $key = 'product_cat_' . $term->term_id;
                $is_rest = function_exists( 'get_field' ) ? get_field( 'e_restaurante', $key ) : null;
                if ( $is_rest ) {
                    $valor = function_exists( 'get_field' ) ? get_field( 'valor_saco', $key ) : null;
                    $restaurant_rows[] = array(
                        'id'    => $term->term_id,
                        'name'  => $term->name,
                        'valor' => $valor,
                        'edit_link' => get_edit_term_link( $term->term_id, 'product_cat', 'product' ),
                    );
                }
            }
        }
        if ( ! empty( $restaurant_rows ) ) {
            usort( $restaurant_rows, function( $a, $b ) {
                return strcasecmp( $a['name'], $b['name'] );
            } );
        }
        echo '<h2>' . esc_html__( 'Restaurantes', 'slap-bag-per-category' ) . '</h2>';
        echo '<table class="widefat striped">';
        echo '<thead><tr><th>' . esc_html__( 'Categoria', 'slap-bag-per-category' ) . '</th><th>' . esc_html__( 'Valor do saco', 'slap-bag-per-category' ) . '</th></tr></thead>';
        echo '<tbody>';
        if ( ! empty( $restaurant_rows ) ) {
            foreach ( $restaurant_rows as $row ) {
                $valor = $row['valor'];
                $valor_num = $this->normalize_pt_decimal( $valor );
                $display = ( $valor !== null && $valor !== '' ) ? ( number_format( floatval( $valor_num ), 2, ',', '.' ) . ' €' ) : esc_html__( 'Sem valor', 'slap-bag-per-category' );
                $name_html = $row['edit_link'] ? '<a href="' . esc_url( $row['edit_link'] ) . '">' . esc_html( $row['name'] ) . '</a>' : esc_html( $row['name'] );
                echo '<tr><td>' . $name_html . '</td><td>' . esc_html( $display ) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="2">' . esc_html__( 'Nenhuma categoria de restaurante encontrada.', 'slap-bag-per-category' ) . '</td></tr>';
        }
        echo '</tbody></table>';
        echo '</div>';
    }
}
