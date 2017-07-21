<?php

/**
 * Abstract Rex Product Feed Generator
 *
 * A abstract class definition that includes functions used for generating xml feed.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 * The XML Feed Generator.
 *
 * This is used to generate xml feed based on given settings.
 *
 * @since      1.0.0
 * @package    Rex_Product_Feed_Abstract_Generator
 * @author     RexTheme <info@rextheme.com>
 */
abstract class Rex_Product_Feed_Abstract_Generator {

	/**
	 * The Product/Feed ID.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    id    Feed id.
	 */
	protected $id;

	/**
	 * Feed Title.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    title    Feed title
	 */
	protected $title;

	/**
	 * Feed Description.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    desc    Feed description.
	 */
	protected $desc;

	/**
	 * Feed Link.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    link    Feed link.
	 */
	protected $link;

  /**
   * The feed rules containig all attributes and their value mappings for the feed.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Product_Feed_Abstract_Generator    $feed_rules    Contains attributes and value mappings for the feed.
   */
  protected $feed_rules;

	/**
	 * The Product Query args to retrieve specific products for making the Feed.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    $products_args    Contains products query args for feed.
	 */
	protected $products_args;

	/**
	 * Array contains all products.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Rex_Product_Feed_Abstract_Generator    $products    Contains all products to make feed.
	 */
	protected $products;

  /**
   * Array contains all variable products for creating feed with variations.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Product_Feed_Abstract_Generator    $products    Contains all products to make feed.
   */
  protected $variable_products;

	/**
	 * The Feed.
	 * @since    1.0.0
	 * @access   protected
	 * @var Rex_Product_Feed_Abstract_Generator    $feed    Feed as text.
	 */
	protected $feed;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 * @param $config
	 * @since    1.0.0
	 */
	public function __construct( $config ) {
		$this->prepare_products_args( $config['products'] );
    $this->setup_feed_data( $config['info'] );
		$this->setup_feed_rules( $config['feed_config'] );
		$this->setup_products();
    $this->setup_variable_products();
	}

	/**
	 * Prepare the Products Query args for retrieving  products.
	 * @param $args
	 */
	protected function prepare_products_args( $args ) {

		$this->products_args = array(
      'post_type'              => 'product',
      'fields'                 => 'ids',
      'posts_per_page'         => -1,
      'update_post_term_cache' => false,
      'update_post_meta_cache' => false,
      'cache_results'          => false,
		);

		if ( $args['products_scope'] === 'custom'){
			$this->products_args['post__in'] = $args['items'];
		}elseif ( $args['products_scope'] !== 'all') {
			$terms = $args['products_scope'] === 'product_tag' ? 'tags' : 'cats';

			$this->products_args['tax_query'][] = array(
				'taxonomy' => $args['products_scope'],
				'field'    => 'slug',
				'terms'    => $args[$terms]
			);
		}
	}

	/**
	 * Setup the Feed Related info
	 * @param $info
	 */
	protected function setup_feed_data( $info ){
		$this->id    = $info['post_id'];
		$this->title = $info['title'];
		$this->desc  = $info['desc'];
		$this->link  = esc_url( home_url('/') );
	}

  /**
   * Setup the rules
   * @param $info
   */
  protected function setup_feed_rules( $info ){
    $feed_rules       = array();
    parse_str( $info, $feed_rules );

    $feed_rules       = $feed_rules['fc'];
    $this->feed_rules = $feed_rules;

    // save the feed_rules into feed post_meta.
    update_post_meta( $this->id, 'rex_feed_feed_config', $this->feed_rules );
  }


	/**
	 * Get the products to generate feed
	 */
	protected function setup_products() {

		$this->products = get_posts( $this->products_args );

	}

  /**
   * Setup the variable products from products array.
   */
  protected function setup_variable_products() {

    $this->variable_products = array();

    // Loop through all products and separate the variable products.
    foreach( $this->products as $product_id ) {
      if( $this->is_variable_product( $product_id ) ){
        $this->variable_products[] = $product_id;
      }
    }

    // remove variable products from products array
    if ( !empty( $this->variable_products ) ) {
      $this->products = array_diff( $this->products, $this->variable_products );
    }

  }

  /**
   * Setup the variable products from products array.
   */
  protected function is_variable_product( $product_id = false ) {

    if ( false === $product_id ) {
      return false;
    }

    $product = wc_get_product( $product_id );

    if( $product->is_type( 'variable' ) ){
      return true;
    }

    return false;
  }


	/**
	 * Get Product data.
	 * @param bool $id
	 *
	 * @return array
	 */
	protected function get_product_data( $product_id = false ){
		$data = new Rex_Product_Data_Retriever( $product_id, $this->feed_rules );
    return $data->get_all_data();
	}

	/**
	 * Save the feed as XML file.
	 *
	 * @return bool
	 */
	protected function save_feed(){
		$path  = wp_upload_dir();
		$path  = $path['basedir'] . '/rex-feed';

		// make directory if not exist
		if ( !file_exists($path) ) {
			wp_mkdir_p($path);
		}

		$file = trailingslashit($path) . "feed-{$this->id}.xml";

		return file_put_contents($file, $this->feed) ? 'true' : 'false';
	}

	/**
	 * Responsible for creating the feed.
	 * @return string
	 **/
	abstract public function make_feed();

}
