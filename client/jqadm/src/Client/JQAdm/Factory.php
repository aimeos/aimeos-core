<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage JQAdm
 */


namespace Aimeos\Client\JQAdm;


/**
 * Common factory for JQAdm clients.
 *
 * @package Client
 * @subpackage JQAdm
 */
class Factory
{
	/**
	 * Creates a new client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Shop context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $type Type of the client, e.g 'product' for \Aimeos\Client\JQAdm\Product\Standard
	 * @param string|null $name Client name (default: "Standard")
	 * @return \Aimeos\Client\JQAdm\Iface HTML client implementing \Aimeos\Client\JQAdm\Iface
	 * @throws \Aimeos\Client\JQAdm\Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( \Aimeos\MShop\Context\Item\Iface $context, array $templatePaths, $type, $name = null )
	{
		if( empty( $type ) ) {
			throw new \Aimeos\Client\JQAdm\Exception( sprintf( 'Client JQAdm type is empty' ) );
		}

		if( ctype_alnum( $type ) === false ) {
			throw new \Aimeos\Client\JQAdm\Exception( sprintf( 'Invalid characters in client name "%1$s"', $type ) );
		}

		$factory = '\\Aimeos\\Client\\JQAdm\\' . ucwords( $type ) . '\\Factory';

		if( class_exists( $factory ) === false ) {
			throw new \Aimeos\Client\JQAdm\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
		}

		$client = @call_user_func_array( array( $factory, 'createClient' ), array( $context, $templatePaths, $name ) );

		if( $client === false ) {
			throw new \Aimeos\Client\JQAdm\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
		}

		return $client;
	}

}