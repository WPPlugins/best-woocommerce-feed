<?php
/**
 * The Rex_Product_Feed_Factory class file that
 * returns a feed generator object based on selected merchant.
 *
 * @link       https://rextheme.com
 * @since      1.0.0
 *
 * @package    Rex_Product_Feed_Factory
 * @subpackage Rex_Product_Feed_Factory/includes
 */
class Rex_Product_Feed_Factory {

	public static function build( $config ){
		$className = 'Rex_Product_Feed_'. ucfirst( $config['merchant'] );

		if( $config == '' || ! class_exists( $className ) ) {
			throw new Exception('Invalid Merchant.');
		} else {
			return new $className( $config );
		}

		return false;
	}



}
