<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs\Common\Factory;


/**
 * Common methods for all controller factories.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Base
{
	private static $objects = [];


	/**
	 * Injects a controller object.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Controller\Jobs\Iface|null $controller Frontend controller object
	 */
	public static function injectController( string $classname, \Aimeos\Controller\Jobs\Iface $controller = null )
	{
		self::$objects[$classname] = $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Bootstrap $aimeos Aimeos Bootstrap object
	 * @param \Aimeos\Controller\Jobs\Iface $controller Controller object
	 * @param array $decorators List of decorator names that should be wrapped around the controller object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Controller\Jobs\Attribute\Decorator\"
	 * @return \Aimeos\Controller\Jobs\Iface Controller object
	 */
	protected static function addDecorators( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		\Aimeos\Controller\Jobs\Iface $controller, array $decorators, string $classprefix ) : \Aimeos\Controller\Jobs\Iface
	{
		$iface = \Aimeos\Controller\Jobs\Common\Decorator\Iface::class;

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$controller = new $classname( $controller, $context, $aimeos );

			if( !( $controller instanceof $iface ) ) {
				throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param \Aimeos\Controller\Jobs\Iface $controller Controller object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return \Aimeos\Controller\Jobs\Iface Controller object
	 */
	protected static function addControllerDecorators( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		\Aimeos\Controller\Jobs\Iface $controller, string $domain ) : \Aimeos\Controller\Jobs\Iface
	{
		if( !is_string( $domain ) || $domain === '' ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$localClass = str_replace( '/', '\\', ucwords( $domain, '/' ) );
		$config = $context->getConfig();

		/** controller/jobs/common/decorators/default
		 * Configures the list of decorators applied to all job controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created controllers:
		 *
		 *  controller/jobs/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all controller instances in that order. The decorator classes would be
		 * "\Aimeos\Controller\Jobs\Common\Decorator\Decorator1" and
		 * "\Aimeos\Controller\Jobs\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'controller/jobs/common/decorators/default', [] );
		$excludes = $config->get( 'controller/jobs/' . $domain . '/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\Aimeos\Controller\Jobs\Common\Decorator\\';
		$controller = self::addDecorators( $context, $aimeos, $controller, $decorators, $classprefix );

		$classprefix = '\Aimeos\Controller\Jobs\Common\Decorator\\';
		$decorators = $config->get( 'controller/jobs/' . $domain . '/decorators/global', [] );
		$controller = self::addDecorators( $context, $aimeos, $controller, $decorators, $classprefix );

		$classprefix = '\Aimeos\Controller\Jobs\\' . ucfirst( $localClass ) . '\Decorator\\';
		$decorators = $config->get( 'controller/jobs/' . $domain . '/decorators/local', [] );
		$controller = self::addDecorators( $context, $aimeos, $controller, $decorators, $classprefix );

		return $controller;
	}


	/**
	 * Creates a controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @return \Aimeos\Controller\Jobs\Iface Controller object
	 */
	protected static function createController( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos,
		string $classname, string $interface ) : \Aimeos\Controller\Jobs\Iface
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$controller = new $classname( $context, $aimeos );

		if( !( $controller instanceof $interface ) ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
