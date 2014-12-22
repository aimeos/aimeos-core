<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic hash implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Helper_Password_Hash implements MShop_Common_Item_Helper_Password_Interface
{
	private $_options = array();
	

	/**
	 * Initializes the password helper.
	 * 
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		if( !isset( $options['algorithm'] ) || !in_array( $options['algorithm'], hash_algos(), true ) ) {
			throw new MShop_Exception( sprintf( 'The algorithm "%1$s" is not supported', $options['algorithm'] ) );
		}
		
		$this->_options = $options;
	}


	/**
	 * Returns the hashed password.
	 *
	 * @return string Hashed password
	 */
	public function encode( $password, $salt )
	{
		$encode = isset( $this->_options['base64'] ) && $this->_options['base64'] == true;
		$format = ( isset( $this->_options['format'] ) ? $this->_options['format'] : '%1$s%2$s' );
		$iterations = ( isset( $this->_options['iterations'] ) ? (int) $this->_options['iterations'] : 1 );
		
		$salted = sprintf( $format, $password, $salt );
		$digest = hash( $this->_options['algorithm'], $salted, true);
		
		// "stretch" hash
		for ($i = 1; $i < $iterations; $i++ ) {
			$digest = hash( $this->_options['algorithm'], $digest . $salted, true);
		}
		
		return ( $encode ? base64_encode( $digest ) : bin2hex( $digest ) );
	}
}
