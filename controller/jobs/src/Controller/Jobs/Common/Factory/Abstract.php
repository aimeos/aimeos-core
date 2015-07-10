<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Common methods for all factories.
 *
 * @package Controller
 * @subpackage Jobs
 */
abstract class Controller_Jobs_Common_Factory_Abstract
{
	private static $_objects = array();


	/**
	 * Injects a controller object.
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param Controller_Jobs_Interface|null $controller Frontend controller object
	 */
	public static function injectController( $classname, Controller_Jobs_Interface $controller = null )
	{
		self::$_objects[$classname] = $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Arcavias $arcavias Arcavias object
	 * @param Controller_Jobs_Interface $controller Controller object
	 * @param string $classprefix Decorator class prefix, e.g. "Controller_Jobs_Attribute_Decorator_"
	 * @return Controller_Jobs_Common_Interface Controller object
	 */
	protected static function _addDecorators( MShop_Context_Item_Interface $context, Arcavias $arcavias,
		Controller_Jobs_Interface $controller, array $decorators, $classprefix )
	{
		$iface = 'Controller_Jobs_Common_Decorator_Interface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false )
			{
				$classname = is_string($name) ? $classprefix . ucfirst( strtolower( $name ) ) : '<not a string>';
				throw new Controller_Jobs_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
			}

			$classname = $classprefix . ucfirst( strtolower( $name ) );

			if( class_exists( $classname ) === false ) {
				throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$controller =  new $classname( $context, $arcavias, $controller );

			if( !( $controller instanceof $iface ) ) {
				throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $controller;
	}


	/**
	 * Adds the decorators to the controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Arcavias $arcavias Arcavias object
	 * @param Controller_Jobs_Interface $controller Controller object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return Controller_Jobs_Common_Interface Controller object
	 */
	protected static function _addControllerDecorators( MShop_Context_Item_Interface $context, Arcavias $arcavias,
		Controller_Jobs_Interface $controller, $domain )
	{
		if ( !is_string( $domain ) || $domain === '' ) {
			throw new Controller_Jobs_Exception( sprintf( 'Invalid domain "%1$s"', $domain ) );
		}

		$subdomains = explode('/', $domain);
		$domain = $localClass = $subdomains[0];
		if (count($subdomains) > 1) {
			$localClass = str_replace(' ', '_', ucwords( implode(' ', $subdomains) ) );
		}

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
		 * "Controller_Jobs_Common_Decorator_Decorator1" and
		 * "Controller_Jobs_Common_Decorator_Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'controller/jobs/common/decorators/default', array() );
		$excludes = $config->get( 'controller/jobs/' . $domain . '/decorators/excludes', array() );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[ $key ] );
			}
		}

		$classprefix = 'Controller_Jobs_Common_Decorator_';
		$controller =  self::_addDecorators( $context, $arcavias, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_Jobs_Common_Decorator_';
		$decorators = $config->get( 'controller/jobs/' . $domain . '/decorators/global', array() );
		$controller =  self::_addDecorators( $context, $arcavias, $controller, $decorators, $classprefix );

		$classprefix = 'Controller_Jobs_'. $localClass . '_Decorator_';
		$decorators = $config->get( 'controller/jobs/' . $domain . '/decorators/local', array() );
		$controller =  self::_addDecorators( $context, $arcavias, $controller, $decorators, $classprefix );

		return $controller;
	}


	/**
	 * Creates a controller object.
	 *
	 * @param MShop_Context_Item_Interface $context Context instance with necessary objects
	 * @param Arcavias $arcavias Arcavias object
	 * @param string $classname Name of the controller class
	 * @param string $interface Name of the controller interface
	 * @return Controller_Jobs_Common_Interface Controller object
	 */
	protected static function _createController( MShop_Context_Item_Interface $context, Arcavias $arcavias,
		$classname, $interface )
	{
		if( isset( self::$_objects[$classname] ) ) {
			return self::$_objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$controller =  new $classname( $context, $arcavias );

		if( !( $controller instanceof $interface ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
