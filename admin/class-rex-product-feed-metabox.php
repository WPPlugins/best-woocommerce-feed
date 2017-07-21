<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Metabox
 * @subpackage Rex_Product_Feed/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines all the Metaboxes for Products
 *
 * @package    Rex_Product_Metabox
 * @subpackage Rex_Product_Feed/admin
 * @author     RexTheme <info@rextheme.com>
 */
class Rex_Product_Metabox {

    private $prefix = 'rex_feed_';

    /**
     * Register all metaboxes.
     *
     * @since    1.0.0
     */
    public function register() {
        $this->products();
        $this->feed_config();
        $this->feed_file();
    }

    /**
     * Products Selection Metabox
     *
     * @since    1.0.0
     */
    private function products(){

        $box = new_cmb2_box( array(
            'id'            => $this->prefix . 'products',
            'title'         => esc_html__( 'Products', 'rex-product-feed' ),
            'object_types'  => array( 'product-feed' ), // Post type
        ) );

        $box->add_field( array(
            'name'             => __('Products', 'rex-product-feed' ),
            'desc'             => __('Select products to create feed for.', 'rex-product-feed' ),
            'id'               => $this->prefix . 'products',
            'type'             => 'select',
            'show_option_none' => false,
            'default'          => 'all',
            'options'          => array(
                'all'    => __( 'All Published Products', 'rex-product-feed' ),
                'product_cat'   => __( 'Map Category', 'rex-product-feed' ),
                'product_tag'   => __( 'Map Tag', 'rex-product-feed' ),
            ),
        ) );

        $box->add_field( array(
            'name'           => 'Product Category',
            'desc'           => 'Description Goes Here',
            'id'             => $this->prefix . 'cats',
            'taxonomy'       => 'product_cat', //Enter Taxonomy Slug
            'type'           => 'taxonomy_multicheck',
            'text'           => array(
                'no_terms_text' => 'Sorry, no product categories could be found.'
            ),
            'attributes' => array(
                'data-conditional-id'    => $this->prefix . 'products',
                'data-conditional-value' => 'product_cat',
            ),
        ) );

        $box->add_field( array(
            'name'           => 'Product Tag',
            'desc'           => 'Description Goes Here',
            'id'             => $this->prefix . 'tags',
            'taxonomy'       => 'product_tag', //Enter Taxonomy Slug
            'type'           => 'taxonomy_multicheck',
            'text'           => array(
                'no_terms_text' => 'Sorry, no product tags could be found.'
            ),
            'attributes' => array(
                'data-conditional-id'    => $this->prefix . 'products',
                'data-conditional-value' => 'product_tag',
            ),
        ) );
    }

    /**
     * Defines Metaboxes for Feed Configuration
     *
     * @return void
     * @author Khorshed Alam
     **/
    private function feed_config(){
        $box = new_cmb2_box( array(
            'id'            => $this->prefix . 'conf',
            'title'         => esc_html__( 'Feed Configuration', 'rex-product-feed' ),
            'object_types'  => array( 'product-feed' ), // Post type
        ) );

        $box->add_field( array(
            'name'             => __('Merchant Type', 'rex-product-feed' ),
            'desc'             => __('Select Merchant Type of the Feed.', 'rex-product-feed' ),
            'id'               => $this->prefix . 'merchant',
            'type'             => 'select',
            'show_option_none' => false,
            'default'          => 'all',
            'options'          => array(
                'google'    => __( 'Google Shopping', 'rex-product-feed' ),
            ),
        ) );

        $box->add_field( array(
            'id'        => $this->prefix . 'config_heading',
            'name'      => 'Configure Feed Attributes and values.',
            'type'      => 'title',
            'after_row' => array($this, 'atts_config_cb'),
        ) );

    }

    /**
     * Display Feed Config Metabox.
     *
     * @return void
     * @author Khorshed Alam
     **/
    public function atts_config_cb($field_args, $field){
      $feed_rules    = get_post_meta( $field->object_id, $this->prefix . 'feed_config', true );
      $feed_template = new Rex_Feed_Template_Google($feed_rules);
      require plugin_dir_path( __FILE__ ) . 'partials/feed-config-metabox-display.php';
    }

    /**
     * Defines Metaboxes for Feed
     *
     * @return void
     * @author Khorshed Alam
     **/
    private function feed_file(){
        $box = new_cmb2_box( array(
            'id'            => $this->prefix . 'file',
            'title'         => esc_html__( 'XML Feed', 'rex-product-feed' ),
            'object_types'  => array( 'product-feed' ), // Post type
            'context'       => 'side',
        ) );

        $box->add_field( array(
            'name'             => __('', 'rex-product-feed' ),
            'desc'             => __('Your XML Feed URL', 'rex-product-feed' ),
            'id'               => $this->prefix . 'xml_file',
            'type'             => 'text',
            'sanitization_cb'  => array($this, 'sanitize_xml_file'),
            'after_field'      => array($this, 'after_field_xml_file_cb'),
            'default'          => '',
            'attributes'  => array(
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ),
        ) );

    }

    public function after_field_xml_file_cb($field_args, $field){
        $feed_url = get_post_meta( $field->object_id, $this->prefix . 'xml_file', true );
        // Only show feed url not empty.
        if ( strlen($feed_url) > 0 ){
            $url = esc_url( get_post_meta( $field->object_id, 'rex_feed_xml_file', true ) );
            echo '<a target="_blank" class="btn waves-effect waves-light" href="' . $url . '">
              <i class="material-icons">open_in_new</i>View Feed</a> ';
            echo '<a target="_blank" class="btn waves-effect waves-light" href="' . $url . '" download>
            <i class="material-icons">system_update_alt</i>Download</a>';
        }
    }


    /**
     * Update the XML File URL on Sanitization Hook.
     *
     * @return string
     * @author Khorshed Alam
     **/
    public function sanitize_xml_file($value, $field_args, $field){
        $path  = wp_upload_dir();
        $path  = $path['baseurl'] . '/rex-feed' . "/feed-{$field->object_id}.xml";
        return esc_url( $path );
    }

}
