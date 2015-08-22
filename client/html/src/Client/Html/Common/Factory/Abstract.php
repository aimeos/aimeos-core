<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
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
	private static $_objects = array();


	/**
	 * Injects a client object.
	 * The object is returned via createClient() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param Client_Html_Interface|null $client ExtJS client object
	 */
	public static function injectClient( $classname, Client_Html_Interface $client = null )
	{
		self::$_objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Client_Html_Interface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "Client_Html_Catalog_Decorator_"
	 * @return Client_Html_Interface Client object
	 */
	protected static function _addDecorators( MShop_Context_Item_Interface $context,
		Client_Html_Interface $client, array $templatePaths, array $decorators, $classprefix )
	{
		$iface = 'Client_Html_Common_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new Client_Html_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new Client_Html_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client =  new $classname( $context, $templatePaths, $client );

			if( !( $client instanceof $iface ) ) {
				throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Client_Html_Interface $client Client object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return Client_Html_Interface Client object
	 */
	protected static function _addClientDecorators( MShop_Context_Item_Interface $context,
		Client_Html_Interface $client, array $templatePaths, $domain )
	{
		if ( !is_string( $domain ) || $domain === '' ) {
			throw new Client_Html_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$localClass = str_replace( ' ', '_', ucwords( str_replace( '/', ' ', $domain ) ) );
		$config = $context->getConfig();

		/** client/html/common/decorators/default
		 * Configures the list of decorators applied to all html clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  client/html/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "Client_Html_Common_Decorator_Decorator1" and
		 * "Client_Html_Common_Decorator_Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'client/html/common/decorators/default', array() );
		$excludes = $config->get( 'client/html/' . $domain . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[ $key ] );
			}
		}

		$classprefix = 'Client_Html_Common_Decorator_';
		$client =  self::_addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		$classprefix = 'Client_Html_Common_Decorator_';
		$decorators = $config->get( 'client/html/' . $domain . '/decorators/global', array() );
		$client =  self::_addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		$classprefix = 'Client_Html_' . $localClass . '_Decorator_';
		$decorators = $config->get( 'client/html/' . $domain . '/decorators/local', array() );
		$client =  self::_addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		return $client;
	}


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
		if( isset( self::$_objects[$classname] ) ) {
			return self::$_objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$client =  new $classname( $context, $templatePaths );

		if( !( $client instanceof $interface ) ) {
			throw new Client_Html_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $client;
	}
}
