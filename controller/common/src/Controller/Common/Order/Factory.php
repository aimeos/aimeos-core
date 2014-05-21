<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Common
 */


/**
 * Common order controller factory.
 *
 * @package Controller
 * @subpackage Common
 */
class Controller_Common_Order_Factory
{
	private static $_objects = array();


	/**
	 * Injects a controller object.
	 *
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param Controller_Frontend_Interface|null $controller Frontend controller object
	 */
	public static function injectController( $classname, Controller_Common_Order_Interface $controller = null )
	{
		self::$_objects[$classname] = $controller;
	}


	/**
	 * Creates a new controller specified by the given name.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param Arcavias $arcavias Arcavias object
	 * @param string|null $name Name of the controller or "Default" if null
	 * @return Controller_Common_Order_Interface New order controller object
	 * @throws Controller_Common_Exception
	 */
	public static function createController( MShop_Context_Item_Interface $context, $name = null )
	{
		/** classes/controller/common/order/name
		 * Class name of the used order common controller implementation
		 *
		 * Each default common controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the controller factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  Controller_Common_Order_Default
		 *
		 * and you want to replace it with your own version named
		 *
		 *  Controller_Common_Order_Myorder
		 *
		 * then you have to set the this configuration option:
		 *
		 *  classes/controller/common/order/name = Myorder
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyOrder"!
		 *
		 * @param string Last part of the class name
		 * @since 2014.07
		 * @category Developer
		 */
		if ( $name === null ) {
			$name = $context->getConfig()->get('classes/controller/common/order/name', 'Default');
		}

		if ( ctype_alnum($name) === false ) {
			$classname = is_string($name) ? 'Controller_Common_Order_' . $name : '<not a string>';
			throw new Controller_Common_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'Controller_Common_Order_Interface';
		$classname = 'Controller_Common_Order_' . $name;

		if( isset( self::$_objects[$classname] ) ) {
			return self::$_objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new Controller_Common_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$controller =  new $classname( $context );

		if( !( $controller instanceof $interface ) ) {
			throw new Controller_Common_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $controller;
	}
}
