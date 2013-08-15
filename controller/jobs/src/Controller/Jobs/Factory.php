<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://www.arcavias.com/en/license
 * @package Controller
 * @subpackage Jobs
 */


/**
 * Factory which can create all job controllers.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Controller_Jobs_Factory
{
	static private $_prefix = 'Controller_Jobs';
	static private $_controllers = array();


	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * Controllers are created by providing only the domain name, e.g.
	 * "stock" for the Controller_Jobs_Stock_Default.
	 * Please note, that only the default controllers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * controller to hand over specifc implementation names.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param string $path Name of the domain
	 * @throws Controller_Jobs_Exception If the given path is invalid or the controllers wasn't found
	 */
	static public function createController( MShop_Context_Item_Interface $context, $path )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new Controller_Jobs_Exception( sprintf( 'Controller path is empty' ) );
		}

		$siteid = $context->getLocale()->getSiteId();

		if( !isset( self::$_controllers[$siteid][$path] ) )
		{
			$parts = explode( '/', $path );

			foreach( $parts as $key => $part )
			{
				if( ctype_alnum( $part ) === false ) {
					throw new Controller_Jobs_Exception( sprintf( 'Invalid controller "%1$s" in "%2$s"', $part, $path ) );
				}

				$parts[$key] = ucwords( $part );
			}

			$factory = 'Controller_Jobs_' . join( '_', $parts ) . '_Factory';

			if( class_exists( $factory ) === false ) {
				throw new Controller_Jobs_Exception( sprintf( 'Class "%1$s" not found', $factory ) );
			}

			$controller = call_user_func_array( array( $factory, 'createController' ), array( $context ) );

			if( $controller === false ) {
				throw new Controller_Jobs_Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
			}

			self::$_controllers[$siteid][$path] = $controller;
		}

		return self::$_controllers[$siteid][$path];
	}


	/**
	 * Returns all available controller instances.
	 *
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param array $cntlPaths Associative list of the base path as key and all
	 * 	relative job controller paths (core and extensions)
	 * @return array Associative list of controller names as key and the class instance as value
	 */
	static public function getControllers( MShop_Context_Item_Interface $context, array $cntlPaths )
	{
		$cntlList = array();
		$subFolder = str_replace( '_', DIRECTORY_SEPARATOR, self::$_prefix );

		foreach( $cntlPaths as $path => $list )
		{
			foreach( $list as $relpath )
			{
				$path .= DIRECTORY_SEPARATOR . $relpath . DIRECTORY_SEPARATOR . $subFolder;

				if( is_dir( $path ) )
				{
					$it = new DirectoryIterator( $path );
					$list = self::_createControllers( $it, $context );

					$cntlList = array_merge( $cntlList, $list );
				}
			}
		}

		return $cntlList;
	}


	/**
	 * Instantiates all found factories and stores the controller instances in the class variable.
	 *
	 * @param DirectoryIterator $dir Iterator over the (sub-)directory which might contain a factory
	 * @param MShop_Context_Item_Interface $context Context object required by controllers
	 * @param string $prefix Part of the class name between "Controller_Jobs" and "Factory"
	 * @throws Controller_Jobs_Exception If factory name is invalid or if the controller couldn't be instantiated
	 */
	static protected function _createControllers( DirectoryIterator $dir, $context, $prefix = '' )
	{
		$list = array();

		foreach( $dir as $entry )
		{
			if( $entry->getType() === 'dir' && $entry->isDot() === false )
			{
				$name = strtolower( $entry->getBaseName() );
				$it = new DirectoryIterator( $entry->getPathName() );
				$subList = self::_createControllers( $it, $context, ( $prefix !== '' ? $prefix . '/' : '' ) . $name );

				$list = array_merge( $list, $subList );
			}
			else if( $prefix !== '' && $entry->getType() === 'file'
				&& ( $name = $entry->getBaseName( '.php' ) ) === 'Factory' )
			{
				$list[$prefix] = self::createController( $context, $prefix );
			}
		}

		return $list;
	}
}
