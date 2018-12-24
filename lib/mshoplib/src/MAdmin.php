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
	static private $cache = true;
	static private $objects = [];


	/**
	 * Enables or disables caching of class instances
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function cache( $value )
	{
		$old = self::$cache;
		self::$cache = (boolean) $value;

		return $old;
	}


	/**
	 * Removes all manager objects from the cache
	 */
	static public function clear()
	{
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
	static public function create( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Manager path is empty' ) );
		}

		$id = (string) $context;

		if( !isset( self::$objects[$id][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid characters in manager name "%1$s" in "%2$s"', $part, $path ) );
				}
			}

			if( ( $name = array_shift( $parts ) ) === null ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Manager path "%1$s" is invalid', $path ) );
			}


			$factory = '\Aimeos\MAdmin\\' . ucwords( $name ) . '\Manager\Factory';

			if( class_exists( $factory ) === false ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
			}

			$manager = @call_user_func_array( array( $factory, 'createManager' ), array( $context ) );

			if( $manager === false ) {
				throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$objects[$id][$name] = $manager;
		}

		return self::$objects[$id][$path];
	}


	/**
	 * Injects a manager object for the given path of manager names
	 *
	 * This method is for testing only and you must call \Aimeos\MAdmin::clear()
	 * afterwards!
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Manager object for the given manager path
	 */
	static public function inject( \Aimeos\MShop\Context\Item\Iface $context, $path, \Aimeos\MShop\Common\Manager\Iface $object )
	{
		self::$objects[(string) $context][$path] = $object;
	}
}
