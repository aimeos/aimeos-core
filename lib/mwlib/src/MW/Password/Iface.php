<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MW
 * @subpackage Password
 */


namespace Aimeos\MW\Password;


/**
 * Generic interface for the passwort helper
 *
 * @package MW
 * @subpackage Password
 */
interface Iface
{
	/**
	 * Returns the hashed password
	 *
	 * @param string $password Clear text password string
	 * @return string Hashed password
	 */
	public function hash( string $password ) : string;


	/**
	 * Verifies the password
	 *
	 * @param string $password Clear text password string
	 * @param string $hash Hashed password
	 * @return bool TRUE if password and hash match
	 */
	public function verify( string $password, string $hash ) : bool;
}
