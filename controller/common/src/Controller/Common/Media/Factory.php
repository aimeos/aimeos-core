<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2016-2020
 * @package Controller
 * @subpackage Common
 */


namespace Aimeos\Controller\Common\Media;


/**
 * Common media controller factory.
 *
 * @package Controller
 * @subpackage Common
 */
class Factory
{
	private static $objects = [];


	/**
	 * Creates a new controller specified by the given name.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param string|null $name Name of the controller or "Standard" if null
	 * @return \Aimeos\Controller\Common\Media\Iface New media controller object
	 * @throws \Aimeos\Controller\Common\Exception
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, string $name = null ) : Iface
	{
		/** controller/common/media/name
		 * Class name of the used media common controller implementation
		 *
		 * Each default common controller can be replace by an alternative imlementation.
		 * To use this implementation, you have to set the last part of the class
		 * name as configuration value so the controller factory knows which class it
		 * has to instantiate.
		 *
		 * For example, if the name of the default class is
		 *
		 *  \Aimeos\Controller\Common\Media\Standard
		 *
		 * and you want to replace it with your own version named
		 *
		 *  \Aimeos\Controller\Common\Media\Mymedia
		 *
		 * then you have to set the this configuration option:
		 *
		 *  controller/common/media/name = Mymedia
		 *
		 * The value is the last part of your own class name and it's case sensitive,
		 * so take care that the configuration value is exactly named like the last
		 * part of the class name.
		 *
		 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
		 * characters are possible! You should always start the last part of the class
		 * name with an upper case character and continue only with lower case characters
		 * or numbers. Avoid chamel case names like "MyMedia"!
		 *
		 * @param string Last part of the class name
		 * @since 2016.01
		 * @category Developer
		 */
		if( $name === null ) {
			$name = $context->getConfig()->get( 'controller/common/media/name', 'Standard' );
		}

		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\Aimeos\Controller\Common\Media\\' . $name : '<not a string>';
			throw new \Aimeos\Controller\Common\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = \Aimeos\Controller\Common\Media\Iface::class;
		$classname = 'Aimeos\Controller\Common\Media\\' . $name;

		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\Controller\Common\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$controller = new $classname( $context );

		if( !( $controller instanceof $iface ) ) {
			throw new \Aimeos\Controller\Common\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $controller;
	}


	/**
	 * Injects a controller object.
	 *
	 * The object is returned via createController() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param null|\Aimeos\Controller\Common\Media\Iface $controller Frontend controller object
	 */
	public static function inject( string $classname, \Aimeos\Controller\Common\Media\Iface $controller = null )
	{
		self::$objects[trim( $classname, '\\' )] = $controller;
	}
}
