<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage JsonAdm
 */


namespace Aimeos\Controller\JsonAdm\Common\Factory;


/**
 * Common methods for all JSON API factories
 *
 * @package Controller
 * @subpackage JsonAdm
 */
class Base
{
	private static $objects = array();


	/**
	 * Injects a controller object
	 *
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Controller\JsonAdm\Iface|null $controller JSON API controller object
	 */
	public static function injectController( $classname, \Aimeos\Controller\JsonAdm\Iface $controller = null )
	{
		self::$objects[$classname] = $controller;
	}


	/**
	 * Adds the decorators to the JSON API controller object
	 *
	 * @param \Aimeos\Controller\JsonAdm\Common\Iface $controller Controller object
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Controller\JsonAdm\Common\Iface Controller object
	 */
	protected static function addControllerDecorators( \Aimeos\Controller\JsonAdm\Iface $controller,
		\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		$config = $context->getConfig();

		/** controller/jsonadm/common/decorators/default
		 * Configures the list of decorators applied to all JSON API controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  controller/jsonadm/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Controller\JsonAdm\Common\Decorator\Decorator1" and
		 * "\Aimeos\Controller\JsonAdm\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2015.12
		 * @category Developer
		 */
		$decorators = $config->get( 'controller/jsonadm/common/decorators/default', array() );

		$classprefix = '\\Aimeos\\Controller\\JsonAdm\\Common\\Decorator\\';
		$controller = self::addDecorators( $controller, $decorators, $classprefix, $context, $view, $templatePaths, $path );

		if( $path !== null && is_string( $path ) )
		{
			$excludes = $config->get( 'controller/jsonadm/' . $path . '/decorators/excludes', array() );
			$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $path ) ) );

			foreach( $decorators as $key => $name )
			{
				if( in_array( $name, $excludes ) ) {
					unset( $decorators[$key] );
				}
			}

			$classprefix = '\\Aimeos\\Controller\\JsonAdm\\Common\\Decorator\\';
			$decorators = $config->get( 'controller/jsonadm/' . $path . '/decorators/global', array() );
			$controller = self::addDecorators( $controller, $decorators, $classprefix, $context, $view, $templatePaths, $path );

			$classprefix = '\\Aimeos\\Controller\\JsonAdm\\' . ucfirst( $localClass ) . '\\Decorator\\';
			$decorators = $config->get( 'controller/jsonadm/' . $path . '/decorators/local', array() );
			$controller = self::addDecorators( $controller, $decorators, $classprefix, $context, $view, $templatePaths, $path );
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object
	 *
	 * @param \Aimeos\Controller\JsonAdm\Iface $controller Controller object
	 * @param array $decorators List of decorator names
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Controller\JsonAdm\Product\Decorator\"
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Controller\JsonAdm\Common\Iface Controller object
	 */
	protected static function addDecorators( \Aimeos\Controller\JsonAdm\Iface $controller, array $decorators, $classprefix,
			\Aimeos\MShop\Context\Item\Iface $context, \Aimeos\MW\View\Iface $view, $templatePaths, $path )
	{
		$iface = '\\Aimeos\\Controller\\JsonAdm\\Common\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Invalid class name "%1$s"', $classname ), 404 );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ), 404 );
			}

			$controller = new $classname( $controller, $context, $view, $templatePaths, $path );

			if( !( $controller instanceof $iface ) ) {
				throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ), 404 );
			}
		}

		return $controller;
	}


	/**
	 * Creates a new controller object
	 *
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object
	 * @param \Aimeos\MW\View\Iface $view View object
	 * @param array $templatePaths List of file system paths where the templates are stored
	 * @param string $path Name of the controller separated by slashes, e.g "product/stock"
	 * @return \Aimeos\Controller\JsonAdm\Common\Iface Controller object
	 */
	protected static function createControllerBase( $classname, $interface, \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MW\View\Iface $view, array $templatePaths, $path )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Class "%1$s" not found', $classname ), 404 );
		}

		$controller = new $classname( $context, $view, $templatePaths, $path );

		if( !( $controller instanceof $interface ) ) {
			throw new \Aimeos\Controller\JsonAdm\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ), 500 );
		}

		return $controller;
	}
}
