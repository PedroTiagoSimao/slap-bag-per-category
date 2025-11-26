<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @since      1.0.0
 * @package    SLAP_Bag_Per_Category
 * @subpackage SLAP_Bag_Per_Category/public
 * @author     Pedro Simão <info@slap.pt>
 */
class SLAP_Bag_Per_Category_Public {

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

    private $printed_embalagem = false;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of the plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
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

        $css_path = SLAP_BAG_PER_CATEGORY_PLUGIN_DIR . 'public/css/slap-bag-per-category-public.css';
        $ver = $this->version;
        if ( file_exists( $css_path ) ) {
            $mtime = filemtime( $css_path );
            if ( $mtime ) {
                $ver = $mtime;
            }
        }
        wp_enqueue_style(
            $this->plugin_name,
            SLAP_BAG_PER_CATEGORY_PLUGIN_URL . 'public/css/slap-bag-per-category-public.css',
            array(),
            $ver,
            'all'
        );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
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
            SLAP_BAG_PER_CATEGORY_PLUGIN_URL . 'public/js/slap-bag-per-category-public.js', 
            array( 'jquery' ), 
            $this->version, 
            false 
        );
    }

    public function render_embalagem_product_meta() {
        if ( $this->printed_embalagem ) {
            return;
        }
        if ( ! function_exists( 'get_field' ) ) {
            return;
        }

        global $product;
        if ( ! $product ) {
            return;
        }

        $product_id = $product->get_id();
        if ( ! $product_id ) {
            return;
        }

        $embalagem = get_field( 'embalagem', $product_id );
        if ( ! $embalagem && method_exists( $product, 'get_parent_id' ) && $product->get_parent_id() ) {
            $parent_id = $product->get_parent_id();
            if ( $parent_id ) {
                $embalagem = get_field( 'embalagem', $parent_id );
            }
        }

        if ( ! $embalagem ) {
            return;
        }

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

        echo '<div class="slap-embalagem">
                <span class="slap-embalagem-label">' . esc_html__( 'Embalagem', 'slap-bag-per-category' ) . ':</span>
                <span class="slap-embalagem-value">' . esc_html( $display_value ) . '</span>
            </div>';
        $this->printed_embalagem = true;
    }
}
