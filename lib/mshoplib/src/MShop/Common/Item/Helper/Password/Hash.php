<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2014-2015
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\Helper\Password;


/**
 * Generic hash implementation of the password helper item.
 *
 * @package MShop
 * @subpackage Common
 */
class Hash implements \Aimeos\MShop\Common\Item\Helper\Password\Iface
{
	private $options = [];
	

	/**
	 * Initializes the password helper.
	 *
	 * @param array Associative list of key/value pairs of options specific for the hashing method
	 */
	public function __construct( array $options )
	{
		if( !function_exists( 'hash' ) ) {
			throw new \Aimeos\MShop\Exception( 'The PHP "hash" extension is not available. Please install it before you can use the hash() function' );
		}

		if( !isset( $options['algorithm'] ) || !in_array( $options['algorithm'], hash_algos(), true ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'The algorithm "%1$s" is not supported', $options['algorithm'] ) );
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
		$encode = isset( $this->options['base64'] ) && $this->options['base64'] == true;
		$format = ( isset( $this->options['format'] ) ? $this->options['format'] : '%1$s%2$s' );
		$iterations = ( isset( $this->options['iterations'] ) ? (int) $this->options['iterations'] : 1 );
		
		$salted = sprintf( $format, $password, $salt );
		$digest = hash( $this->options['algorithm'], $salted, true);
		
		// "stretch" hash
		for ($i = 1; $i < $iterations; $i++ ) {
			$digest = hash( $this->options['algorithm'], $digest . $salted, true);
		}
		
		return ( $encode ? base64_encode( $digest ) : bin2hex( $digest ) );
	}
}
