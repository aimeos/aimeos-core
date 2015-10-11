<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015
 * @package Controller
 * @subpackage ExtJS
 */


namespace Aimeos\Controller\ExtJS;


/**
 * Factory which can create all ExtJS controllers.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Factory
{
	static private $cache = true;
	static private $controllers = array();


	/**
	 * Removes the controller objects from the cache.
	 *
	 * If neither a context ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer $id Context ID the objects have been created with (string of \Aimeos\MShop\Context\Item\Iface)
	 * @param string $path Path describing the controller to clear, e.g. "product/lists/type"
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
	 * "product" for the \Aimeos\Controller\ExtJS\Product\Standard or a path of names to
	 * retrieve a specific sub-controller, e.g. "product/type" for the
	 * \Aimeos\Controller\ExtJS\Product\Type\Standard controller.
	 * Please note, that only the default controllers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * controller to hand over specifc implementation names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @throws \Aimeos\Controller\ExtJS\Exception If the given path is invalid or the manager wasn't found
	 */
	static public function createController( \Aimeos\MShop\Context\Item\Iface $context, $path )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Controller path is empty' ) );
		}

		$id = (string) $context;

		if( self::$cache === false || !isset( self::$controllers[$id][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $key => $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid controller "%1$s" in "%2$s"', $part, $path ) );
				}

				$parts[$key] = ucwords( $part );
			}

			$factory = '\\Aimeos\\Controller\\ExtJS\\' . join( '\\', $parts ) . '\\Factory';

			if( class_exists( $factory ) === false ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Class "%1$s" not found', $factory ) );
			}

			$controller = @call_user_func_array( array( $factory, 'createController' ), array( $context ) );

			if( $controller === false ) {
				throw new \Aimeos\Controller\ExtJS\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$controllers[$id][$path] = $controller;
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
