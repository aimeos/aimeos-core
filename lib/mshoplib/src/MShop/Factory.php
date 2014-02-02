<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package MShop
 */


/**
 * Factory which can create all MShop managers.
 *
 * @package MShop
 */
class MShop_Factory
{
	static private $_managers = array();


	/**
	 * Removes all manager objects from the cache.
	 *
	 * If neither a site ID nor a path is given, the complete cache will be pruned.
	 *
	 * @param integer $siteid ID of the site to clear
	 * @param string $path Path describing the manager to clear, e.g. "product/list/type"
	 */
	static public function clear( $siteid = null, $path = null )
	{
		if( $siteid !== null )
		{
			if( $path !== null ) {
				self::$_managers[$siteid][$path] = null;
			} else {
				self::$_managers[$siteid] = array();
			}

			return;
		}

		self::$_managers = array();
	}


	/**
	 * Creates the required manager specified by the given path of manager names.
	 *
	 * Domain managers are created by providing only the domain name, e.g.
	 * "product" for the MShop_Product_Manager_Default or a path of names to
	 * retrieve a specific sub-manager, e.g. "product/type" for the
	 * MShop_Product_Manager_Type_Default manager.
	 * Please note, that only the default managers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * domain or the getSubManager() method to hand over specifc implementation
	 * names.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by managers
	 * @param string $path Name of the domain (and sub-managers) separated by slashes, e.g "product/list"
	 * @throws MShop_Exception If the given path is invalid or the manager wasn't found
	 */
	static public function createManager( MShop_Context_Item_Interface $context, $path )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new MShop_Exception( sprintf( 'Manager path is empty' ) );
		}

		$siteid = $context->getLocale()->getSiteId();

		if( !isset( self::$_managers[$siteid][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new MShop_Exception( sprintf( 'Invalid characters in manager name "%1$s" in "%2$s"', $part, $path ) );
				}
			}

			if( ( $name = array_shift( $parts ) ) === null ) {
				throw new MShop_Exception( sprintf( 'Manager path "%1$s" is invalid', $path ) );
			}


			if( !isset( self::$_managers[$siteid][$name] ) )
			{
				$factory = 'MShop_' . ucwords( $name ) . '_Manager_Factory';

				if( class_exists( $factory ) === false ) {
					throw new MShop_Exception( sprintf( 'Class "%1$s" not available', $factory ) );
				}

				$manager = @call_user_func_array( array( $factory, 'createManager' ), array( $context ) );

				if( $manager === false ) {
					throw new MShop_Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
				}

				self::$_managers[$siteid][$name] = $manager;
			}


			foreach( $parts as $part )
			{
				$tmpname = $name .  '/' . $part;

				if( !isset( self::$_managers[$siteid][$tmpname] ) ) {
					self::$_managers[$siteid][$tmpname] = self::$_managers[$siteid][$name]->getSubManager( $part );
				}

				$name = $tmpname;
			}
		}

		return self::$_managers[$siteid][$path];
	}
}
