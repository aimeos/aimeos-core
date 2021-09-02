<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2021
 * @package MW
 * @subpackage Password
 */


namespace Aimeos\MW\Password;


/**
 * Standard implementation of the password helper
 *
 * @package MW
 * @subpackage Password
 */
class Standard implements \Aimeos\MW\Password\Iface
{
	/**
	 * Returns the hashed password
	 *
	 * @param string $password Clear text password string
	 * @return string Hashed password
	 */
	public function hash( string $password ) : string
	{
		return password_hash( $password, PASSWORD_DEFAULT );
	}


	/**
	 * Verifies the password
	 *
	 * @param string $password Clear text password string
	 * @param string $hash Hashed password
	 * @return bool TRUE if password and hash match
	 */
	public function verify( string $password, string $hash ) : bool
	{
		return password_verify( $password, $hash );
	}
}
