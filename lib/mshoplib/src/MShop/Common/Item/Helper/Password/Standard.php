<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Default implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class Standard implements \Aimeos\MShop\Common\Item\Helper\Password\Iface
{
	private $options = [];
	

	/**
	 * Initializes the password helper.
	 * 
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
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
		$format = ( isset( $this->options['format'] ) ? $this->options['format'] : '%1$s%2$s' );

		return sha1( sprintf( $format, $password, $salt ) );
	}
}
