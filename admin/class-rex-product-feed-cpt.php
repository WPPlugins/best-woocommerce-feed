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
class Rex_Product_CPT {

    /**
     * Register all metaboxes.
     *
     * @since    1.0.0
     */
    public function register() {
        $this->post_types();
    }

    /**
     * Metabox for Google Merchant.
     *
     * @since    1.0.0
     */
    private function post_types(){
        register_extended_post_type( 'product-feed', array(
            'rewrite'            => false,
            'query_var'          => false,
            'publicly_queryable' => false,
            'supports'           => array( 'title' ),
            'enter_title_here'   => 'Enter feed title here',
            'admin_cols' => array(

                'merchant' => array(
                    'title'       => 'Merchant',
                    'meta_key'    => 'rex_feed_merchant',
                    'function'    => function (){
                        echo ucwords( esc_html( get_post_meta( get_the_id(), 'rex_feed_merchant', true ) ) );
                    }
                ),

                'xml_feed' => array(
                    'title'       => 'Feed URL',
                    'meta_key'    => 'rex_feed_xml_file',
                ),

                'view_feed' => array(
                    'title'       => 'View/Download',
                    'function'    => function (){
                        $url = esc_url( get_post_meta( get_the_id(), 'rex_feed_xml_file', true ) );
                        echo '<a target="_blank" class="button" href="' . $url . '">View</a> ';
                        echo '<a target="_blank" class="button" href="' . $url . '" download>Download</a>';
                    }
                ),

                'date'
            ),
        ));
    }

}
