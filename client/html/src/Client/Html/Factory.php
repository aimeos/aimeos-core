<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


namespace Aimeos\Client\Html;


/**
 * Common factory for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Factory
{
	/**
	 * Creates a new client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $type Type of the client, e.g 'account/favorite' for \Aimeos\Client\Html\Account\Favorite\Standard
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\Html\Iface HTML client implementing \Aimeos\Client\Html\Iface
	 * @throws \Aimeos\Client\Html\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $type, $name = null )
	{
		if( empty( $type ) ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Client HTML type is empty' ) );
		}

		$parts = explode( '/', $type );

		if( count( $parts ) !== 2 ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Client type "%1$s" must consist of two parts separated by "/"', $type ) );
		}

		foreach( $parts as $part )
		{
			if( ctype_alnum( $part ) === false ) {
				throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid characters in client name "%1$s" in "%2$s"', $part, $type ) );
			}
		}

		$factory = '\\Aimeos\\Client\\Html\\' . ucwords( $parts[0] ) . '\\' . ucwords( $parts[1] ) . '\\Factory';

		if( class_exists( $factory ) === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
		}

		$client = @call_user_func_array( array( $factory, 'createClient' ), array( $context, $templatePaths, $name ) );

		if( $client === false ) {
			throw new \Aimeos\Client\Html\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
		}

		return $client;
	}

}