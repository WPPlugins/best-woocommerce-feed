<?php

/**
 * Class for retriving product data based on user selected feed configuration.
 *
 * Get the product data based on feed config selected by user.
 *
 * @package    Rex_Product_Data_Retriever
 * @subpackage Rex_Product_Feed/admin
 * @author     RexTheme <info@rextheme.com>
 */
class Rex_Product_Data_Retriever {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $feed_rules;

    /**
     * Contains all available meta keys for products.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $product_meta_keys;

    /**
     * The data of product retrived by feed_rules.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $data    The current version of this plugin.
     */
    private $data;


    /**
     * Metabox instance of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $metabox    The current metabox of this plugin.
     */
    private $product;

    /**
     * Variant atts for feed.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $metabox    The current metabox of this plugin.
     */
    private $variant_atts = array( 'color', 'pattern', 'material', 'age_group', 'gender', 'size', 'size_type', 'size_system' );

    /**
     * Additional images of current product.
     *
     * @since    1.0.0
     * @access   private
     * @var      object    $metabox    The current metabox of this plugin.
     */
    private $additional_images = array();

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $product, $feed_rules ) {
        $this->product           = wc_get_product( $product );
        $this->feed_rules        = $feed_rules;
        $this->product_meta_keys = Rex_Feed_Attributes::get_attributes();
        // $this->set_test_feed_rules(); // only for testing purpose of all atts values;
        $this->set_all_value();
        $this->maybe_set_variation_data();
    }


    /**
     * Setup Testing feed rules for every attributes.
     * Just to check if this class return proper values.
     *
     * @since    1.0.2
     */
    public function set_test_feed_rules() {
        $this->feed_rules = array();

        foreach ($this->product_meta_keys as $key_cat => $attrs) {
            foreach ($attrs as $key => $attr) {
                $this->feed_rules[] = array(
                    'attr'     => $key,
                    'type'     => 'meta',
                    'meta_key' => $key,
                    'st_value' => '',
                    'prefix'   => '',
                    'suffix'   => '',
                    'escape'   => 'default',
                    'limit'    => 0,
                );
            }
        }

    }

    /**
     * Retrive and setup all data for every feed rules.
     *
     * @since    1.0.0
     */
    public function set_all_value() {
        $this->data = array();

        foreach ($this->feed_rules as $key => $rule) {
            $this->data[ $rule['attr'] ] = $this->set_val( $rule );
        }

    }


    /**
     * Set value for a single feed rule.
     *
     * @since    1.0.0
     */
    public function set_val( $rule ) {
        $val = '';

        if ( 'static' === $rule['type'] ) {
            $val = $rule['st_value'];

        }elseif ( 'meta' === $rule['type'] && $this->is_primary_attr( $rule['meta_key'] ) ) {
            $val = $this->set_pr_att( $rule['meta_key']  );

        }elseif ( 'meta' === $rule['type'] && $this->is_image_attr( $rule['meta_key'] ) ) {
            $val = $this->set_image_att( $rule['meta_key']  );
        }

        // maybe add prefix/suffix
        $val = $this->maybe_add_prefix_suffix($val, $rule);
        // maybe escape
        $val = $this->maybe_escape($val, $rule);
        // maybe limit
        $val = $this->maybe_limit($val, $rule['limit']);

        return $val;

    }

    /**
     * Return all data.
     *
     * @since    1.0.0
     */
    public function get_all_data() {
        return $this->data;
    }

    /**
     * Set a primary attribute.
     *
     * @since    1.0.0
     */
    private function set_pr_att( $key ) {
        switch ( $key ) {
            case 'id':
                return $this->product->get_id(); break;

            case 'sku':
                return $this->product->get_sku(); break;

            case 'title':
                return $this->product->get_title(); break;

            case 'price':
                return $this->product->get_price(); break;

            case 'sale_price':
                return $this->product->get_sale_price(); break;

            case 'description':
                if(($this->is_children())):
                    $_product = wc_get_product( $this->product->post->post_parent );
                    $_product_desc =  $_product->get_description();
                    return $_product_desc;
                else:
                    return $this->product->get_description();
                endif;
                break;

            case 'short_description':
                return $this->product->get_short_description(); break;

            case 'product_cats':
                return $this->get_product_cats(); break;

            case 'product_tags':
                return $this->get_product_tags(); break;

            case 'link':
                return $this->product->get_permalink(); break;

            case 'condition':
                return $this->get_condition(); break;

            case 'availability':
                return $this->get_availability(); break;

            case 'quantity':
                return $this->product->get_stock_quantity(); break;

            case 'weight':
                return $this->product->get_weight(); break;

            case 'width':
                return $this->product->get_width(); break;

            case 'height':
                return $this->product->get_height(); break;

            case 'length':
                return $this->product->get_length(); break;

            case 'type':
                return $this->product->get_type(); break;

            case 'rating_average':
                return $this->product->get_average_rating(); break;

            case 'rating_total':
                return $this->product->get_rating_count(); break;

            case 'sale_price_dates_from':
                return date( get_option( 'date_format' ), $this->product->get_date_on_sale_from() ); break;

            case 'sale_price_dates_to':
                return date( get_option( 'date_format' ), $this->product->get_date_on_sale_to() ); break;

            default: return ''; break;
        }
    }


    /**
     * Set a Image attribute.
     *
     * @since    1.0.0
     */
    private function set_image_att( $key ) {
        switch ( $key ) {
            case 'featured_image':
                return wp_get_attachment_url(  $this->product->get_image_id() ); break;

            default: return $this->get_additional_image( $key ); break;
        }
    }

    /**
     * Get additional image url by key.
     *
     * @since    1.0.0
     */
    private function get_additional_image( $key ) {

        if ( empty( $this->additional_images ) ) {
            $this->set_additional_images();
        }

        if ( array_key_exists( $key, $this->additional_images ) ) {
            return $this->additional_images[$key];
        }

        return '';

    }

    /**
     * Retrieve a product's categories as a list with specified format.
     *
     * @param string $before Optional. Before list.
     * @param string $sep Optional. Separate items using this.
     * @param string $after Optional. After list.
     * @return string|false
     */
    private function get_product_cats( $before = '', $sep = ', ', $after = '' ) {
        return $this->get_the_term_list( $this->product->get_id(), 'product_cat', $before, $sep, $after );
    }

    /**
     * Retrieve a product's tags as a list with specified format.
     *
     *
     * @param string $before Optional. Before list.
     * @param string $sep Optional. Separate items using this.
     * @param string $after Optional. After list.
     * @return string|false
     */
    private function get_product_tags( $before = '', $sep = ', ', $after = '' ) {
        return $this->get_the_term_list( $this->product->get_id(), 'product_tag', $before, $sep, $after );
    }

    /**
     * Retrieve a product's terms as a list with specified format.
     *
     *
     * @param int $id Product ID.
     * @param string $taxonomy Taxonomy name.
     * @param string $before Optional. Before list.
     * @param string $sep Optional. Separate items using this.
     * @param string $after Optional. After list.
     * @return string|false
     */
    private function get_the_term_list( $id, $taxonomy, $before = '', $sep = '', $after = '' ) {
        $terms = get_the_terms( $id, $taxonomy );

        if ( empty( $terms ) || is_wp_error( $terms ) ){
            return '';
        }

        $term_names = array();

        foreach ( $terms as $term ) {
            $term_names[] = $term->name;
        }

        ksort($term_names);

        return $before . join( $sep, $term_names ) . $after;
    }

    /**
     * Set additional images url.
     *
     * @since    1.0.0
     */
    private function set_additional_images() {

        $img_ids = $this->product->get_gallery_image_ids();

        $images = array();
        if ( ! empty( $img_ids ) ) {

            foreach ($img_ids as $key => $img_id) {
                $img_key = 'image_' . ($key+1);
                $images[$img_key] = wp_get_attachment_url($img_id);
            }

            // set images to the property
            $this->additional_images = $images;
        }

    }

    /**
     * Helper to check if a attribute is a Primary Attribute.
     *
     * @since    1.0.0
     */
    private function is_primary_attr( $key ) {
        return array_key_exists( $key, $this->product_meta_keys['Primary Attributes'] );
    }

    /**
     * Helper to check if a attribute is a Image Attribute.
     *
     * @since    1.0.0
     */
    private function is_image_attr( $key ) {
        return array_key_exists( $key, $this->product_meta_keys['Image Attributes'] );
    }


    /**
     * Helper to get condtion of a product.
     *
     * @since    1.0.0
     */
    private function get_condition( ) {
        return 'New';
    }

    /**
     * Helper to get availability of a product
     *
     * @since    1.0.0
     */
    private function get_availability( ) {
        if ( $this->product->is_in_stock() == TRUE ) {
            return 'in stock';
        } else {
            return 'out of stock';
        }
    }

    /**
     * Add neccessary prefix/suffix to a value.
     *
     * @since    1.0.0
     */
    private function maybe_add_prefix_suffix($val, $rule) {
        $prefix =  $rule['prefix'];
        $suffix =  $rule['suffix'];

        if ( !empty( $prefix ) ) {
            $val = $prefix . $val;
        }

        if ( !empty( $suffix ) ) {
            $val = $val . $suffix;
        }

        return $val;
    }

    /**
     * Escape a value with specific escape method.
     *
     * @since    1.0.0
     */
    private function maybe_escape($val, $escape) {
        return $val;
    }


    /**
     * Limit the output chars to specified length.
     *
     * @since    1.0.0
     */
    private function maybe_limit($val, $limit) {
        $limit = (int) $limit;
        if ( $limit > 0) {
            return substr($val, 0, $limit);
        }
        return $val;
    }

    /**
     * Setup variation data if current product is a variable product.
     *
     * @since    1.0.0
     */
    private function maybe_set_variation_data() {

        if ( 'WC_Product_Variation' != get_class($this->product) ) {
            return;
        }

        $variant_atts = $this->product->get_variation_attributes();

        foreach ($variant_atts as $key => $value) {
            $key = str_replace( 'attribute_pa_', '', $key);

            if( in_array($key, $this->variant_atts) ){
                $this->data[$key] = $value;
            }
        }

    }


    /**
     * Check if this product is child product or not
     *
     * @return bool
     * @since    1.0.0
     */
    private function is_children(){
        return $this->product->post->post_parent? true: false;
    }

}
