<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package MShop
 */


namespace Aimeos\Controller\Frontend;


/**
 * Factory which can create all Frontend controllers.
 *
 * @package \Aimeos\Controller\Frontend
 */
class Factory
{
	static private $cache = true;
	static private $controllers = array();


	/**
	 * Removes all controller objects from the cache.
	 *
	 * If neither a context ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer $id Context ID the objects have been created with (string of \Aimeos\MShop\Context\Item\Iface)
	 * @param string $path Path describing the controller to clear, e.g. "basket"
	 */
	static public function clear( $id = null, $path = null )
	{
		if( $id !== null )
		{
			if( $path !== null ) {
				self::$controllers[$id][$path] = null;
			} else {
				self::$controllers[$id] = array();
			}

			return;
		}

		self::$controllers = array();
	}


	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * Controllers are created by providing only the domain name, e.g.
	 * "basket" for the \Aimeos\Controller\Frontend\Basket\Standard or a path of names to
	 * retrieve a specific sub-controller if available.
	 * Please note, that only the default controllers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * controller to hand over specifc implementation names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "basket"
	 * @throws \Aimeos\Controller\Frontend\Exception If the given path is invalid or the manager wasn't found
	 */
	static public function createController( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		if( empty( $path ) ) {
			throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Controller path is empty' ) );
		}

		$id = (string) $context;

		if( self::$cache === false || !isset( self::$controllers[$id][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $key => $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Invalid characters in controller name "%1$s" in "%2$s"', $part, $path ) );
				}

				$parts[$key] = ucwords( $part );
			}

			$factory = '\\Aimeos\\Controller\\Frontend\\' . join( '\\', $parts ) . '\\Factory';

			if( class_exists( $factory ) === false ) {
				throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Class "%1$s" not available', $factory ) );
			}

			$manager = call_user_func_array( array( $factory, 'createController' ), array( $context ) );

			if( $manager === false ) {
				throw new \Aimeos\Controller\Frontend\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$controllers[$id][$path] = $manager;
		}

		return self::$controllers[$id][$path];
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
