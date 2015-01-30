<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MShop
 * @subpackage Common
 */


/**
 * Bcrypt implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class MShop_Common_Item_Helper_Password_Bcrypt implements MShop_Common_Item_Helper_Password_Interface
{
	private $_options = array();
	

	/**
	 * Initializes the password helper.
	 * 
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		if( !function_exists( 'password_hash' ) ) {
			throw new MShop_Exception( 'To use the BCrypt encoder, you need to upgrade to PHP 5.5 or install the "ircmaxell/password-compat" via Composer' );
		}

		$this->_options = $options;
	}


	/**
	 * Returns the hashed password.
	 *
	 * @param string $password Clear text password string
	 * @param string|null $salt Password salt
	 * @return string Hashed password
	 */
	public function encode( $password, $salt = null )
	{
		$options = $this->_options;

		return password_hash( $password, PASSWORD_BCRYPT, $options );
	}
}
