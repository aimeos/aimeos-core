<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Factory;


/**
 * Common methods for all factories.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
{
	private static $objects = [];


	/**
	 * Injects a manager object.
	 * The object is returned via createManager() if an instance of the class
	 * with the name name is requested.
	 *
	 * @param string $classname Full name of the class for which the object should be returned
	 * @param \Aimeos\MShop\Common\Manager\Iface|null $manager Manager object or null for removing the manager object
	 */
	public static function injectManager( $classname, \Aimeos\MShop\Common\Manager\Iface $manager = null )
	{
		self::$objects[$classname] = $manager;
	}


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param array $decorators List of decorator names that should be wrapped around the manager object
	 * @param string $classprefix Decorator class prefix, e.g. "\Aimeos\MShop\Product\Manager\Decorator\"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected static function addDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Common\Manager\Iface $manager, array $decorators, $classprefix )
	{
		$iface = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\Iface';

		foreach( $decorators as $name )
		{
			if( ctype_alnum( $name ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in class name "%1$s"', $name ) );
			}

			$classname = $classprefix . $name;

			if( class_exists( $classname ) === false ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
			}

			$manager = new $classname( $manager, $context );

			if( !( $manager instanceof $iface ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
			}
		}

		return $manager;
	}


	/**
	 * Adds the decorators to the manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param \Aimeos\MShop\Common\Manager\Iface $manager Manager object
	 * @param string $domain Domain name in lower case, e.g. "product"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected static function addManagerDecorators( \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\MShop\Common\Manager\Iface $manager, $domain )
	{
		$config = $context->getConfig();

		/** mshop/common/manager/decorators/default
		 * Configures the list of decorators applied to all shop managers
		 *
		 * Decorators extend the functionality of a class by adding new aspects
		 * (e.g. log what is currently done), executing the methods of the underlying
		 * class only in certain conditions (e.g. only for logged in users) or
		 * modify what is returned to the caller.
		 *
		 * This option allows you to configure a list of decorator names that should
		 * be wrapped around the original instances of all created managers:
		 *
		 *  mshop/common/manager/decorators/default = array( 'decorator1', 'decorator2' )
		 *
		 * This would wrap the decorators named "decorator1" and "decorator2" around
		 * all controller instances in that order. The decorator classes would be
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator1" and
		 * "\Aimeos\MShop\Common\Manager\Decorator\Decorator2".
		 *
		 * @param array List of decorator names
		 * @since 2014.03
		 * @category Developer
		 */
		$decorators = $config->get( 'mshop/common/manager/decorators/default', [] );
		$excludes = $config->get( 'mshop/' . $domain . '/manager/decorators/excludes', [] );

		foreach( $decorators as $key => $name )
		{
			if( in_array( $name, $excludes ) ) {
				unset( $decorators[$key] );
			}
		}

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\MShop\\Common\\Manager\\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/decorators/global', [] );
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		$classprefix = '\\Aimeos\\MShop\\' . ucfirst( $domain ) . '\\Manager\\Decorator\\';
		$decorators = $config->get( 'mshop/' . $domain . '/manager/decorators/local', [] );
		$manager = self::addDecorators( $context, $manager, $decorators, $classprefix );

		return $manager;
	}


	/**
	 * Creates a manager object.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context instance with necessary objects
	 * @param string $classname Name of the manager class
	 * @param string $interface Name of the manager interface
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 */
	protected static function createManagerBase( \Aimeos\MShop\Context\Item\Iface $context, $classname, $interface )
	{
		if( isset( self::$objects[$classname] ) ) {
			return self::$objects[$classname];
		}

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$manager = new $classname( $context );

		if( !( $manager instanceof $interface ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $interface ) );
		}

		return $manager;
	}
}
