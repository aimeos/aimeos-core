<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MShop
 * @subpackage Common
 */


/**
 * Default implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Helper_Password_Default implements MShop_Common_Item_Helper_Password_Interface
{
	private $_options = array();
	

	/**
	 * Initializes the password helper.
	 * 
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		$this->_options = $options;
	}


	/**
	 * Returns the hashed password.
	 *
	 * @return string Hashed password
	 */
	public function encode( $password, $salt )
	{
		$format = ( isset( $this->_options['format'] ) ? $this->_options['format'] : '%1$s%2$s' );

		return sha1( sprintf( $format, $password, $salt ) );
	}
}
