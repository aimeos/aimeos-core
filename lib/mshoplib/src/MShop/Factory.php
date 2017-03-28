<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2013
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MShop
 */


namespace Aimeos\MShop;


/**
 * Factory which can create all MShop managers.
 *
 * @package MShop
 */
class Factory
{
	static private $cache = true;
	static private $managers = [];


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
				self::$managers[$id][$path] = null;
			} else {
				self::$managers[$id] = [];
			}

			return;
		}

		self::$managers = [];
	}


	/**
	 * Creates the required manager specified by the given path of manager names.
	 *
	 * Domain managers are created by providing only the domain name, e.g.
	 * "product" for the \Aimeos\MShop\Product\Manager\Standard or a path of names to
	 * retrieve a specific sub-manager, e.g. "product/type" for the
	 * \Aimeos\MShop\Product\Manager\Type\Standard manager.
	 * Please note, that only the default managers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * domain or the getSubManager() method to hand over specifc implementation
	 * names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager object
	 * @throws \Aimeos\MShop\Exception If the given path is invalid or the manager wasn't found
	 */
	static public function createManager( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\MShop\Exception( sprintf( 'Manager path is empty' ) );
		}

		$id = (string) $context;

		if( self::$cache === false || !isset( self::$managers[$id][$path] ) )
		{
			$subpath = '';
			$parts = explode( '/', $path );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \Aimeos\MShop\Exception( sprintf( 'Invalid characters in manager name "%1$s" in "%2$s"', $part, $path ) );
				}
			}

			if( ( $domain = array_shift( $parts ) ) === null ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Manager path "%1$s" is invalid', $path ) );
			}


			if( self::$cache === false || !isset( self::$managers[$id][$domain] ) )
			{
				$factory = '\\Aimeos\\MShop\\' . ucwords( $domain ) . '\\Manager\\Factory';

				if( class_exists( $factory ) === false ) {
					throw new \Aimeos\MShop\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
				}

				$manager = @call_user_func_array( array( $factory, 'createManager' ), array( $context ) );

				if( $manager === false ) {
					throw new \Aimeos\MShop\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
				}

				self::$managers[$id][$domain] = $manager;
			}


			$tmppath = $domain;

			foreach( $parts as $part )
			{
				$subpath .= $part . '/';
				$classname = $context->getConfig()->get( 'mshop/' . $domain . '/manager/' . $subpath . 'name' );

				if( self::$cache === false || !isset( self::$managers[$id][$tmppath . '/' . $part] ) ) {
					self::$managers[$id][$tmppath . '/' . $part] = self::$managers[$id][$tmppath]->getSubManager( $part, $classname );
				}

				$tmppath .= '/' . $part;
			}
		}

		return self::$managers[$id][$path];
	}


	/**
	 * Injects a manager object for the given path of manager names.
	 *
	 * This method is for testing only and you must call \Aimeos\MShop\Factory::clear()
	 * afterwards!
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @param \Aimeos\MShop\Common\Manager\Iface $object Manager object for the given manager path
	 */
	static public function injectManager( \Aimeos\MShop\Context\Item\Iface $context, $path, \Aimeos\MShop\Common\Manager\Iface $object )
	{
		$id = (string) $context;
		self::$managers[$id][$path] = $object;
	}


	/**
	 * Enables or disables caching of class instances.
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function setCache( $value )
	{
		$old = self::$cache;
		self::$cache = (boolean) $value;

		return $old;
	}
}
