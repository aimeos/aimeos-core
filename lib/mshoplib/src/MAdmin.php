<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 */


namespace Aimeos;


/**
 * Factory which can create all MAdmin managers
 *
 * @package MAdmin
 */
class MAdmin
{
	private static $cache = true;
	private static $objects = [];


	/**
	 * Enables or disables caching of class instances and clears cache
	 *
	 * @param boolean $value True to enable caching, false to disable it
	 */
	public static function cache( $value )
	{
		self::$cache = (boolean) $value;
		self::$objects = [];
	}


	/**
	 * Creates the required manager specified by the given path of manager names
	 *
	 * Domain managers are created by providing only the domain name, e.g.
	 * "product" for the \Aimeos\MAdmin\Log\Manager\Standard or a path of names to
	 * retrieve a specific sub-manager.
	 * Please note, that only the default managers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * domain or the getSubManager() method to hand over specifc implementation
	 * names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "log"
	 * @return \Aimeos\MShop\Common\Manager\Iface MAdmin manager object
	 * @throws \Aimeos\MAdmin\Exception If the given path is invalid or the manager wasn't found
	 */
	public static function create( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Manager path is empty' ) );
		}

		if( self::$cache === false || !isset( self::$objects[$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid characters in manager name "%1$s"', $path ) );
				}
			}

			if( ( $name = array_shift( $parts ) ) === null ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Manager path "%1$s" is invalid', $path ) );
			}


			$factory = '\Aimeos\MAdmin\\' . ucfirst( $name ) . '\Manager\Factory';

			if( class_exists( $factory ) === false ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
			}

			if( ( $manager = @call_user_func_array( [$factory, 'create'], [$context] ) ) === false ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$objects[$path] = $manager;
		}

		return self::$objects[$path];
	}


	/**
	 * Injects a manager object for the given path of manager names
	 *
	 * This method is for testing only and you must call \Aimeos\MAdmin::cache( false )
	 * afterwards!
	 *
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Manager object for the given manager path
	 */
	public static function inject( $path, \Aimeos\MShop\Common\Manager\Iface $object )
	{
		self::$objects[$path] = $object;
	}
}
