<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage JsonAdm
 */


namespace Aimeos\Admin\JsonAdm\Common\Factory;


/**
 * Common methods for all JSON API factories
 *
 * @package Admin
 * @subpackage JsonAdm
 */
class Base
{
	private static $objects = array();


	/**
	 * Injects a client object
	 *
	 * The object is returned via createClient() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Admin\JsonAdm\Iface|null $client JSON API client object
	 */
	public static function injectClient( $classname, \Aimeos\Admin\JsonAdm\Iface $client = null )
	{
		self::$objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the JSON API client object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Common\Iface $client Client object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Admin\JsonAdm\Common\Iface Client object
	 */
	protected static function addClientDecorators( \Aimeos\Admin\JsonAdm\Iface $client,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		$config = $context->getConfig();

		/** admin/jsonadm/common/decorators/default
		 * Configures the list of decorators applied to all JSON API clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  admin/jsonadm/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Admin\JsonAdm\Common\Decorator\Decorator1" and
		 * "\Aimeos\Admin\JsonAdm\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 */
		$decorators = $config->get( 'admin/jsonadm/common/decorators/default', array() );

		$classprefix = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
		$client = self::addDecorators( $client, $decorators, $classprefix, $context, $view, $templatePaths, $path );

		if( $path !== null && is_string( $path ) )
		{
			$dpath = trim( $path, '/' );
			$dpath = ( $dpath !== '' ? $dpath . '/' : $dpath );

			$excludes = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/excludes', array() );
			$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );

			foreach( $decorators as $key => $name )
			{
				if( in_array( $name, $excludes ) ) {
					unset( $decorators[$key] );
				}
			}

			$classprefix = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\';
			$decorators = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/global', array() );
			$client = self::addDecorators( $client, $decorators, $classprefix, $context, $view, $templatePaths, $path );

			if( !empty( $path ) )
			{
				$classprefix = '\\Aimeos\\Admin\\JsonAdm\\' . ucfirst( $localClass ) . '\\Decorator\\';
				$decorators = $config->get( 'admin/jsonadm/' . $dpath . 'decorators/local', array() );
				$client = self::addDecorators( $client, $decorators, $classprefix, $context, $view, $templatePaths, $path );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object
	 *
	 * @param \Aimeos\Admin\JsonAdm\Iface $client Client object
	 * @param array $decorators List of decorator names
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Admin\JsonAdm\Product\Decorator\"
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Admin\JsonAdm\Common\Iface Client object
	 */
	protected static function addDecorators( \Aimeos\Admin\JsonAdm\Iface $client, array $decorators, $classprefix,
			\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, $templatePaths, $path )
	{
		$iface = '\\Aimeos\\Admin\\JsonAdm\\Common\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Invalid class name "%1$s"', $classname ), 404 );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ), 404 );
			}

			$client = new $classname( $client, $context, $view, $templatePaths, $path );

			if( !( $client instanceof $iface ) ) {
				throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ), 404 );
			}
		}

		return $client;
	}


	/**
	 * Creates a new client object
	 *
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the client separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Admin\JsonAdm\Common\Iface Client object
	 */
	protected static function createClientBase( $classname, $interface, \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ), 404 );
		}

		$client = new $classname( $context, $view, $templatePaths, $path );

		if( !( $client instanceof $interface ) ) {
			throw new \Aimeos\Admin\JsonAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ), 500 );
		}

		return $client;
	}
}
