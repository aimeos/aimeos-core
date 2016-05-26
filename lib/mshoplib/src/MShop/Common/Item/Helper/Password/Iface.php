<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Generic interface for the passwort helper item.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Initializes the password helper.
	 *
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 * @return null
	 */
	public function __construct( array $options );

	/**
	 * Returns the hashed password.
	 *
	 * @param string $password Clear text password string
	 * @param string|null $salt Password salt
	 * @return string Hashed password
	 */
	public function encode( $password, $salt = null );
}
