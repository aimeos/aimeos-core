<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Bcrypt implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class Bcrypt implements \Aimeos\MShop\Common\Item\Helper\Password\Iface
{
	private $options = [];
	

	/**
	 * Initializes the password helper.
	 * 
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		if( !function_exists( 'password_hash' ) ) {
			throw new \Aimeos\MShop\Exception( 'To use the BCrypt encoder, you need to upgrade to PHP 5.5 or install the "ircmaxell/password-compat" via Composer' );
		}

		$this->options = $options;
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
		$options = $this->options;

		return password_hash( $password, PASSWORD_BCRYPT, $options );
	}
}
