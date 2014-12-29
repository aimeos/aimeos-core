<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014
 * @package MShop
 * @subpackage Common
 */


/**
 * Generic interface for the passwort helper item.
 *
 * @package MShop
 * @subpackage Common
 */
interface MShop_Common_Item_Helper_Password_Interface
{
	/**
	 * Initializes the password helper.
	 *
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options );

	/**
	 * Returns the hashed password.
	 *
	 * @return string Hashed password
	 */
	public function encode( $password, $salt );
}
