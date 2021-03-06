<?php

/**
 * The Google Feed Template class.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Feed
 * @subpackage Rex_Product_Feed/admin/feed-templates/
 */

/**
 *
 * Defines the attributes and template for google feed.
 *
 * @package    Rex_Product_Feed
 * @subpackage Rex_Product_Feed/admin/feed-templates/Rex_Feed_Template_Google
 * @author     RexTheme <info@rextheme.com>
 */
abstract class Rex_Feed_Abstract_Template {

  /**
   * The Feed Attributes.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Feed_Abstract_Template    attributes    Feed attributes.
   */
  protected $attributes;

  /**
   * WooCommerce Product Meta Keys.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Feed_Abstract_Template    attributes    Feed attributes.
   */
  protected $product_meta_keys;

  /**
   * The Feed Template Mappings Attributes and associated value and other constraints.
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Feed_Abstract_Template    template_mappings    Feed attributes mapping for template genaration.
   */
  protected $template_mappings;

  /**
   * Data Sanitization options
   *
   * @since    1.0.0
   * @access   protected
   * @var      Rex_Feed_Abstract_Template    template_mappings    Feed attributes mapping for template genaration.
   */
  protected $sanitization_options;

  /**
   * Set the plugin atts and mapping.
   *
   * @since    1.0.0
   */
  public function __construct( $feed_rules ){
    $this->init_atts();
    $this->init_template_mappings( $feed_rules );
    $this->init_product_meta_keys();
    $this->init_sanitization_options();
  }

  /**
   * Return the attributes
   *
   * @since    1.0.0
   */
  public function getAttributes(){
    return $this->attributes;
  }

  /**
   * Return the template_mappings
   *
   * @since    1.0.0
   */
  public function getTemplateMappings(){
    return $this->template_mappings;
  }

  /**
   * Print attributes as select dropdown.
   *
   * @since    1.0.0
   */
  public function printSelectDropdown( $key, $name, $selected = '' ){

    if ( $name === 'attr' ) {
      $items = $this->attributes;
    }elseif ( $name === 'meta_key' ) {
      $items = $this->product_meta_keys;
    }elseif ( $name === 'escape' ) {
      $items = $this->sanitization_options;
    }else{
      return;
    }

    echo '<select  name="fc['.$key.'][' . esc_attr( $name ) . ']" >';
      echo "<option value=''>Please Select</option>";

    foreach ($items as $groupLabel => $group) {
      if ( !empty($groupLabel)) {
        echo "<optgroup label='$groupLabel'>";
      }

      foreach ($group as $key => $item) {
        if ( $selected == $key ) {
          echo "<option value='$key' selected='selected'>$item</option>";
        }else{
          echo "<option value='$key'>$item</option>";
        }
      }

      if ( !empty($groupLabel)) {
        echo "</optgroup>";
      }
    }

    echo "</select>";
  }

  /**
   * Print attributes Type.
   *
   * @since    1.0.0
   */
  public function printAttType( $key, $select = '' ){
    $options = array( 'meta' => 'Attribute', 'static' => 'Static');
    echo "<select class='type-dropdown' name='fc[$key][type]'>";
    echo "<option value=''>Please Select</option>";
    foreach ($options as $key => $option) {
      $selected = $select === $key ? "selected='selected'" : "";
      echo "<option value='$key' $selected>$option</option>";
    }
    echo "</select>";
  }

  /**
   * Print Prefix input.
   *
   * @since    1.0.0
   */
  public function printInput( $key, $name = '', $val = '' ){
    echo '<input type="text" name="fc['.$key.'][' . esc_attr( $name ) . ']" value="' . esc_attr( $val ) . '"">';
  }

  /**
   * Initialize Product Meta Attributes
   *
   * @since    1.0.0
   */
  protected function init_product_meta_keys(){
    $this->product_meta_keys  = Rex_Feed_Attributes::get_attributes();
  }

  /**
   * Initialize Sanitization Options
   *
   * @since    1.0.0
   */
  protected function init_sanitization_options(){
    $this->sanitization_options = array(
      '' => array(
        'default'                  => 'Default',
        'strip_tags'               => 'Strip Tags',
        'utf_8_encode'             => 'UTF-8 Encode',
        'htmlentities'             => 'htmlentities',
        'integer'                  => 'Integer',
        'price'                    => 'Price',
        'remove_space'             => 'Remove Space',
        'remove_shortcodes'        => 'Remove ShortCodes',
        'remove_special character' => 'Remove Special Character',
        'cdata'                    => 'CDATA'
      )
    );
  }


  /**
   * Initialize Template Mappings with Attributes from feed post_meta.
   *
   * @since    1.0.0
   */
  protected function init_template_mappings( $feed_rules ){
    if ( !empty($feed_rules) && $feed_rules ) {
      $this->template_mappings = $feed_rules;
    }else{
      $this->init_default_template_mappings();
    }
  }

  /**
   * Initialize Attributes
   *
   * @since    1.0.0
   */
  abstract protected function init_atts();

  /**
   * Initialize Default Template Mappings with Attributes.
   *
   * @since    1.0.0
   */
  abstract protected function init_default_template_mappings();

}
