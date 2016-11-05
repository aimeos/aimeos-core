<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 */


namespace Aimeos\MAdmin;


/**
 * Factory which can create all MAdmin managers.
 *
 * @package MAdmin
 */
class Factory
{
	static private $cache = true;
	static private $managers = array();


	/**
	 * Removes all manager objects from the cache.
	 *
	 * If neither a context ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer|null $id Context ID the objects have been created with (string of \Aimeos\MShop\Context\Item\Iface)
	 * @param string|null $path Path describing the manager to clear, e.g. "product/lists/type"
	 */
	static public function clear( $id = null, $path = null )
	{
		if( $id !== null )
		{
			if( $path !== null ) {
				static::$managers[$id][$path] = null;
			} else {
				static::$managers[$id] = array();
			}

			return;
		}

		static::$managers = array();
	}


	/**
	 * Creates the required manager specified by the given path of manager names.
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
	static public function createManager( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\MAdmin\Exception( sprintf( 'Manager path is empty' ) );
		}

		$id = (string) $context;

		if( !isset( static::$managers[$id][$path] ) )
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


			if( !isset( static::$managers[$id][$name] ) )
			{
				$factory = '\\Aimeos\\MAdmin\\' . ucwords( $name ) . '\\Manager\\Factory';

				if( class_exists( $factory ) === false ) {
					throw new \Aimeos\MAdmin\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
				}

				$manager = @call_user_func_array( array( $factory, 'createManager' ), array( $context ) );

				if( $manager === false ) {
					throw new \Aimeos\MAdmin\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
				}

				static::$managers[$id][$name] = $manager;
			}


			foreach( $parts as $part )
			{
				$tmpname = $name . '/' . $part;

				if( !isset( static::$managers[$id][$tmpname] ) ) {
					static::$managers[$id][$tmpname] = static::$managers[$id][$name]->getSubManager( $part );
				}

				$name = $tmpname;
			}
		}

		return static::$managers[$id][$path];
	}


	/**
	 * Enables or disables caching of class instances.
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function setCache( $value )
	{
		$old = static::$cache;
		static::$cache = (boolean) $value;

		return $old;
	}
}
