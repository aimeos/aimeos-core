<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2024
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Password;


/**
 * Default implementation of the password helper
 *
 * @package MShop
 * @subpackage Common
 * @deprecated 2025.01 Use \Aimeos\Base\Password\Iface instead
 */
class Standard implements \Aimeos\MShop\Common\Helper\Password\Iface
{
	private \Aimeos\Base\Password\Iface $password;


	/**
	 * Initializes the password helper.
	 *
	 * @param array $options Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		$this->password = new \Aimeos\Base\Password\Standard();
	}


	/**
	 * Returns the hashed password.
	 *
	 * @param string $password Clear text password string
	 * @param string|null $salt Password salt
	 * @return string Hashed password
	 */
	public function encode( string $password, ?string $salt = null ) : string
	{
		return $this->password->hash( $password );
	}
}
