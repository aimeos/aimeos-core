<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2012
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Common methods for all factories.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Common_Factory_Abstract
{
	private static $objects = array();


	/**
	 * Injects a controller object.
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param Controller_ExtJS_Interface|null $controller ExtJS controller object
	 */
	public static function injectController( $classname, Controller_ExtJS_Interface $controller = null )
	{
		self::$objects[$classname] = $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Controller_ExtJS_Common_Interface $controller Controller object
	 * @param string $classprefix Decorator class prefix, e.g. "Controller_ExtJS_Attribute_Decorator_"
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function addDecorators( MShop_Context_Item_Interface $context,
		Controller_ExtJS_Interface $controller, array $decorators, $classprefix )
	{
		$iface = 'Controller_ExtJS_Common_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string( $name ) ? $classprefix . $name : '<not a string>';
				throw new Controller_ExtJS_Exception( sprintf( 'Invalid class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
			}

			$controller = new $classname( $context, $controller );

			if( !( $controller instanceof $iface ) ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $iface ) );
			}
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Controller_ExtJS_Common_Interface $controller Controller object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function addControllerDecorators( MShop_Context_Item_Interface $context,
		Controller_ExtJS_Interface $controller, $domain )
	{
		if( !is_string( $domain ) || $domain === '' ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$localClass = str_replace( ' ', '_', ucwords( str_replace( '/', ' ', $domain ) ) );
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
		 * "Controller_ExtJS_Common_Decorator_Decorator1" and
		 * "Controller_ExtJS_Common_Decorator_Decorator2".
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

		$classprefix = 'Controller_ExtJS_Common_Decorator_';
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_ExtJS_Common_Decorator_';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/global', array() );
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_ExtJS_' . ucfirst( $localClass ) . '_Decorator_';
		$decorators = $config->get( 'controller/extjs/' . $domain . '/decorators/local', array() );
		$controller = self::addDecorators( $context, $controller, $decorators, $classprefix );

		return $controller;
	}


	/**
	 * Creates a controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @return Controller_ExtJS_Common_Interface Controller object
	 */
	protected static function createControllerBase( MShop_Context_Item_Interface $context, $classname, $interface )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $classname ) );
		}

		$controller = new $classname( $context );

		if( !( $controller instanceof $interface ) ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" does not implement "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
