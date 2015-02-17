<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Client
 * @subpackage Html
 */


/**
 * Common factory for HTML clients.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Factory
{
	/**
	 * Creates a new client object.
	 *
	 * @param MShop_Context_Item_Interface $context Shop context instance with necessary objects
	 * @param array List of file system paths where the templates are stored
	 * @param string $type Type of the client, e.g 'account/favorite' for Client_Html_Account_Favorite_Default
	 * @param string $name Client name (default: "Default")
	 * @return Client_Html_Interface HTML client implementing Client_Html_Interface
	 * @throws Client_Html_Exception If requested client implementation couldn't be found or initialisation fails
	 */
	public static function createClient( MShop_Context_Item_Interface $context, array $templatePaths, $type, $name = null )
	{
		if( empty( $type ) ) {
			throw new Client_Html_Exception( sprintf( 'Client HTML type is empty' ) );
		}
		
		$parts = explode( '/', $type );
		
		if( count( $parts ) !== 2 ) {
			throw new Client_Html_Exception( sprintf( 'Client type "%1$s" must consist of two parts separated by "/"', $type ) );
		}

		foreach( $parts as $part )
		{
			if( ctype_alnum( $part ) === false ) {
				throw new Client_Html_Exception( sprintf( 'Invalid characters in client name "%1$s" in "%2$s"', $part, $type ) );
			}
		}
		
		$factory = 'Client_Html_' . ucwords( $parts[0] ) . '_' . ucwords( $parts[1] ) . '_Factory';
		
		if( class_exists( $factory ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" not available', $factory ) );
		}
		
		$client = @call_user_func_array( array( $factory, 'createClient' ), array( $context, $templatePaths, $name ) );
		
		if( $client === false ) {
			throw new Client_Html_Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
		}

		return $client;
	}

}