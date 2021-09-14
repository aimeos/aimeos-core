<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Password;


/**
 * Generic interface for the passwort helper
 *
 * @package MShop
 * @subpackage Common
 * @deprecated 2022.01
 */
interface Iface
{
	/**
	 * Initializes the password helper.
	 *
	 * @param array $options Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options );

	/**
	 * Returns the hashed password.
	 *
	 * @param string $password Clear text password string
	 * @param string|null $salt Password salt
	 * @return string Hashed password
	 */
	public function encode( string $password, string $salt = null ) : string;
}
