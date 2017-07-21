<?php

/**
 * The file that generates xml feed for Google.
 *
 * A class definition that includes functions used for generating xml feed.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Feed_Google
 * @subpackage Rex_Product_Feed_Google/includes
 * @author     RexTheme <info@rextheme.com>
 */

use LukeSnowden\GoogleShoppingFeed\Containers\GoogleShopping;

class Rex_Product_Feed_Google extends Rex_Product_Feed_Abstract_Generator {

	/**
	 * Create Feed for Google
	 *
	 * @return boolean
	 * @author
	 **/
	public function make_feed() {

		GoogleShopping::title($this->title);
		GoogleShopping::link($this->link);
		GoogleShopping::description($this->desc);

    // Generate feed for both simple and variable products.
    $this->generate_simple_product_feed();
    $this->generate_variable_product_feed();
    $this->feed = GoogleShopping::asRss();

    return $this->save_feed();
	}

  /**
   * Generate Feed data for Simple Products
   **/
  private function generate_simple_product_feed(){
    // Loop through all products.
    foreach( $this->products as $product ) {

      $atts = $this->get_product_data( $product );
      $item = GoogleShopping::createItem();

      // add all attributes for each product.
      foreach ($atts as $key => $value) {
        $item->$key($value); // invoke $key as method of $item object.
      }

    }
  }

  /**
   * Generate Feed data for Variable Products
   **/
  private function generate_variable_product_feed(){
    // Loop through all variable products.
    foreach( $this->variable_products as $product ) {

      $product  = wc_get_product( $product );
      $children =  $product->get_children();

      // add all variants into feed
      foreach ($children as $child) {

        $item = GoogleShopping::createItem();
        $atts = $this->get_product_data( $child );

        // add all attributes for each product.
        foreach ($atts as $key => $value) {
          $item->$key($value); // invoke $key as method of $item object.
        }

        $item->item_group_id( $product->get_id() );

      }
    }
  }

}
