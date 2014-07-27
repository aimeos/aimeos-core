<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage ExtJS
 */


/**
 * Factory which can create all ExtJS controllers.
 *
 * @package Controller
 * @subpackage ExtJS
 */
class Controller_ExtJS_Factory
{
	static private $_cache = true;
	static private $_controllers = array();


	/**
	 * Removes the controller objects from the cache.
	 *
	 * If neither a site ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer $siteid ID of the site to clear
	 * @param string $path Path describing the controller to clear, e.g. "product/list/type"
	 */
	static public function clear( $siteid = null, $path = null )
	{
		if( $siteid !== null )
		{
			if( $path !== null ) {
				self::$_controllers[$siteid][$path] = null;
			} else {
				self::$_controllers[$siteid] = array();
			}

			return;
		}

		self::$_controllers = array();
	}


	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * Controllers are created by providing only the domain name, e.g.
	 * "product" for the Controller_ExtJS_Product_Default or a path of names to
	 * retrieve a specific sub-controller, e.g. "product/type" for the
	 * Controller_ExtJS_Product_Type_Default controller.
	 * Please note, that only the default controllers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * controller to hand over specifc implementation names.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @throws Controller_ExtJS_Exception If the given path is invalid or the manager wasn't found
	 */
	static public function createController( MShop_Context_Item_Interface $context, $path )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new Controller_ExtJS_Exception( sprintf( 'Controller path is empty' ) );
		}

		$id = (string) $context;

		if( self::$_cache === false || !isset( self::$_controllers[$id][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $key => $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new Controller_ExtJS_Exception( sprintf( 'Invalid controller "%1$s" in "%2$s"', $part, $path ) );
				}

				$parts[$key] = ucwords( $part );
			}

			$factory = 'Controller_ExtJS_' . join( '_', $parts ) . '_Factory';

			if( class_exists( $factory ) === false ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Class "%1$s" not found', $factory ) );
			}

			$manager = @call_user_func_array( array( $factory, 'createController' ), array( $context ) );

			if( $manager === false ) {
				throw new Controller_ExtJS_Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$_controllers[$id][$path] = $manager;
		}

		return self::$_controllers[$id][$path];
	}


	/**
	 * Enables or disables caching of class instances.
	 *
	 * @param boolean $value True to enable caching, false to disable it.
	 * @return boolean Previous cache setting
	 */
	static public function setCache( $value )
	{
		$old = self::$_cache;
		self::$_cache = (boolean) $value;

		return $old;
	}
}
