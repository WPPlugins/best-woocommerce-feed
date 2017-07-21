<?php

/**
 * Helper Class to retrive Feed Attributes
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Feed
 * @subpackage Rex_Product_Feed/admin/feed-templates/
 */

/**
 *
 * Defines the attributes for feed.
 *
 * @package    Rex_Product_Feed
 * @subpackage Rex_Product_Feed/admin/feed-templates/Rex_Feed_Attributes
 * @author     RexTheme <info@rextheme.com>
 */
class Rex_Feed_Attributes {

  public static function get_attributes(){
    return array(
      'Primary Attributes'        => array(
        'id'                        => 'Product Id',
        'title'                     => 'Product Title',
        'description'               => 'Product Description',
        'short_description'         => 'Product Short Description',
        'product_cats'              => 'Product Categories',
        'link'                      => 'Product URL',
        'condition'                 => 'Condition',
        'sku'                       => 'SKU',
        'availability'              => 'Availability',
        'quantity'                  => 'Quantity',
        'price'                     => 'Regular Price',
        'sale_price'                => 'Sale Price',
        'weight'                    => 'Weight',
        'width'                     => 'Width',
        'height'                    => 'Height',
        'length'                    => 'Length',
        'rating_total'              => 'Total Rating',
        'rating_average'            => 'Average Rating',
        'product_tags'              => 'Tags',
        'sale_price_dates_from'     => 'Sale Start Date',
        'sale_price_dates_to'       => 'Sale End Date',
      ),

    'Image Attributes' => array(
      'featured_image' => 'Featured Image',
      'image_1'        => 'Additional Image 1',
      'image_2'        => 'Additional Image 2',
      'image_3'        => 'Additional Image 3',
      'image_4'        => 'Additional Image 4',
      'image_5'        => 'Additional Image 5',
      'image_6'        => 'Additional Image 6',
      'image_7'        => 'Additional Image 7',
      'image_8'        => 'Additional Image 8',
      'image_9'        => 'Additional Image 9',
      'image_10'       => 'Additional Image 10',
      ),
    );
  }

}
