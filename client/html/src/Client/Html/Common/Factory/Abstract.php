<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Client
 * @subpackage Html
 */


/**
 * Common methods for all client factories.
 *
 * @package Client
 * @subpackage Html
 */
class Client_Html_Common_Factory_Abstract
{
	/**
	 * Creates a client object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @param array List of file system paths where the templates are stored
	 * @return Client_Html__Interface Client object
	 * @throws Client_Html_Exception If client couldn't be found or doesn't implement the interface
	 */
	protected static function _createClient( MShop_Context_Item_Interface $context, $classname, $interface, $templatePaths )
	{
		if( class_exists( $classname ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$client =  new $classname( $context, $templatePaths );

		if( !( $client instanceof $interface ) ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ) );
		}

		return $client;
	}
}
