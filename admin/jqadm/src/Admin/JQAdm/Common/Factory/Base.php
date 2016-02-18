<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Common\Factory;


/**
 * Common methods for all JQAdm client factories.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Base
{
	private static $objects = array();


	/**
	 * Injects a client object.
	 * The object is returned via createClient() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Admin\JQAdm\Iface|null $client ExtJS client object
	 */
	public static function injectClient( $classname, \Aimeos\Admin\JQAdm\Iface $client = null )
	{
		self::$objects[$classname] = $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Admin\JQAdm\Iface $client Admin object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param array $decorators List of decorator name that should be wrapped around the client
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Admin\JQAdm\Catalog\Decorator\"
	 * @return \Aimeos\Admin\JQAdm\Iface Admin object
	 */
	protected static function addDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Admin\JQAdm\Iface $client, array $templatePaths, array $decorators, $classprefix )
	{
		$iface = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$client = new $classname( $client, $context, $templatePaths );

			if( !( $client instanceof $iface ) ) {
				throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $client;
	}


	/**
	 * Adds the decorators to the client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Admin\JQAdm\Iface $client Admin object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Path of the client in lower case, e.g. "catalog/detail"
	 * @return \Aimeos\Admin\JQAdm\Iface Admin object
	 */
	protected static function addClientDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Admin\JQAdm\Iface $client, array $templatePaths, $path )
	{
		if( !is_string( $path ) || $path === '' ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Invalid domain "%1$s"', $path ) );
		}

		$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );
		$config = $context->getConfig();

		/** admin/jqadm/common/decorators/default
		 * Configures the list of decorators applied to all jqadm clients
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  admin/jqadm/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator1" and
		 * "\Aimeos\Admin\JQAdm\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'admin/jqadm/common/decorators/default', array() );
		$excludes = $config->get( 'admin/jqadm/' . $path . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$client = self::addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\Common\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/global', array() );
		$client = self::addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Admin\\JQAdm\\' . $localClass . '\\Decorator\\';
		$decorators = $config->get( 'admin/jqadm/' . $path . '/decorators/local', array() );
		$client = self::addDecorators( $context, $client, $templatePaths, $decorators, $classprefix );

		return $client;
	}


	/**
	 * Creates a client object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string $classname Name of the client class
	 * @param string $interface Name of the client interface
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @return \Aimeos\Admin\JQAdm\\Iface Admin object
	 * @throws \Aimeos\Admin\JQAdm\Exception If client couldn't be found or doesn't implement the interface
	 */
	protected static function createClientBase( \Aimeos\MShop\Context\Item\Iface $context, $classname, $interface, $templatePaths )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$client = new $classname( $context, $templatePaths );

		if( !( $client instanceof $interface ) ) {
			throw new \Aimeos\Admin\JQAdm\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $client;
	}
}
