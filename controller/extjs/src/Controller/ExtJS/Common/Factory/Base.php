<?php

/**
 * @copyright Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS\Common\Factory;


/**
 * Common methods for all factories.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Base
{
	private static $objects = array();


	/**
	 * Injects a controller object.
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\Controller\ExtJS\Iface|null $controller ExtJS controller object
	 */
	public static function injectController( $classname, \Aimeos\Controller\ExtJS\Iface $controller = null )
	{
		self::$objects[$classname] = $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Controller\ExtJS\Common\Iface $controller Controller object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\Controller\ExtJS\Attribute\Decorator\"
	 * @return \Aimeos\Controller\ExtJS\Common\Iface Controller object
	 */
	protected static function addDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Controller\ExtJS\Iface $controller, array $decorators, $classprefix )
	{
		$iface = '\\Aimeos\\Controller\\ExtJS\\Common\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$controller = new $classname( $controller, $context );

			if( !( $controller instanceof $iface ) ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\Controller\ExtJS\Common\Iface $controller Controller object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return \Aimeos\Controller\ExtJS\Common\Iface Controller object
	 */
	protected static function addControllerDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Controller\ExtJS\Iface $controller, $domain )
	{
		if( !is_string( $domain ) || $domain === '' ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$localClass = str_replace( ' ', '\\', ucwords( str_replace( '/', ' ', $domain ) ) );
		$config = $context->getConfig();

		/** controller/extjs/common/decorators/default
		 * Configures the list of decorators applied to all ExtJS controllers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instance of all created clients:
		 *
		 *  controller/extjs/common/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all client instances in that order. The decorator classes would be
		 * "\Aimeos\Controller\ExtJS\Common\Decorator\Decorator1" and
		 * "\Aimeos\Controller\ExtJS\Common\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'controller/extjs/common/decorators/default', array() );
		$excludes = $config->get( 'controller/extjs/' . $domain . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\Controller\\ExtJS\\Common\\Decorator\\';
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Controller\\ExtJS\\Common\\Decorator\\';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/global', array() );
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\Controller\\ExtJS\\' . ucfirst( $localClass ) . '\\Decorator\\';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/local', array() );
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		return $controller;
	}


	/**
	 * Creates a controller object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @return \Aimeos\Controller\ExtJS\Common\Iface Controller object
	 */
	protected static function createControllerBase( \Aimeos\MShop\Context\Item\Iface $context, $classname, $interface )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$controller = new $classname( $context );

		if( !( $controller instanceof $interface ) ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
