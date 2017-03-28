<?php

/**
 * @copyright Metaways Infosystems GmbH, 2013
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package Controller
 * @subpackage Jobs
 */


namespace Aimeos\Controller\Jobs;


/**
 * Factory which can create all job controllers.
 *
 * @package Controller
 * @subpackage Jobs
 */
class Factory
{
	static private $prefix = '\\Aimeos\\Controller\\Jobs';


	/**
	 * Creates the required controller specified by the given path of controller names.
	 *
	 * Controllers are created by providing only the domain name, e.g.
	 * "stock" for the \Aimeos\Controller\Jobs\Stock\Standard.
	 * Please note, that only the default controllers can be created. If you need
	 * a specific implementation, you need to use the factory class of the
	 * controller to hand over specifc implementation names.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param string $path Name of the domain
	 * @throws \Aimeos\Controller\Jobs\Exception If the given path is invalid or the controllers wasn't found
	 */
	static public function createController( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos, $path )
	{
		$path = strtolower( trim( $path, "/ \n\t\r\0\x0B" ) );

		if( empty( $path ) ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Controller path is empty' ) );
		}

		$parts = explode( '/', $path );

		foreach( $parts as $key => $part )
		{
			if( ctype_alnum( $part ) === false ) {
				throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid controller "%1$s" in "%2$s"', $part, $path ) );
			}

			$parts[$key] = ucwords( $part );
		}

		$factory = '\\Aimeos\\Controller\\Jobs\\' . join( '\\', $parts ) . '\\Factory';

		if( class_exists( $factory ) === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Class "%1$s" not found', $factory ) );
		}

		$controller = call_user_func_array( array( $factory, 'createController' ), array( $context, $aimeos ) );

		if( $controller === false ) {
			throw new \Aimeos\Controller\Jobs\Exception( sprintf( 'Invalid factory "%1$s"', $factory ) );
		}

		return $controller;
	}


	/**
	 * Returns all available controller instances.
	 *
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param array $cntlPaths Associative list of the base path as key and all
	 * 	relative job controller paths (core and extensions)
	 * @return array Associative list of controller names as key and the class instance as value
	 */
	static public function getControllers( \Aimeos\MShop\Context\Item\Iface $context, \Aimeos\Bootstrap $aimeos, array $cntlPaths )
	{
		$cntlList = [];
		$subFolder = trim( str_replace( '\\', '/', self::$prefix ), '/' );

		if( strncmp( $subFolder, 'Aimeos' . '/', 7 ) === 0 ) {
			$subFolder = substr( $subFolder, 7 );
		}

		foreach( $cntlPaths as $path => $list )
		{
			foreach( $list as $relpath )
			{
				$path .= DIRECTORY_SEPARATOR . str_replace( '/', DIRECTORY_SEPARATOR, $relpath . '/' . $subFolder );

				if( is_dir( $path ) )
				{
					$it = new \DirectoryIterator( $path );
					$list = self::createControllers( $it, $context, $aimeos );

					$cntlList = array_merge( $cntlList, $list );
				}
			}
		}

		ksort( $cntlList );

		return $cntlList;
	}


	/**
	 * Instantiates all found factories and stores the controller instances in the class variable.
	 *
	 * @param \DirectoryIterator $dir Iterator over the (sub-)directory which might contain a factory
	 * @param \Aimeos\MShop\Context\Item\Iface $context Context object required by controllers
	 * @param \Aimeos\Bootstrap $aimeos \Aimeos\Bootstrap object
	 * @param string $prefix Part of the class name between "\Aimeos\Controller\Jobs" and "Factory"
	 * @throws \Aimeos\Controller\Jobs\Exception If factory name is invalid or if the controller couldn't be instantiated
	 */
	static protected function createControllers( \DirectoryIterator $dir, \Aimeos\MShop\Context\Item\Iface $context,
		\Aimeos\Bootstrap $aimeos, $prefix = '' )
	{
		$list = [];

		foreach( $dir as $entry )
		{
			if( $entry->getType() === 'dir' && $entry->isDot() === false )
			{
				$name = strtolower( $entry->getBaseName() );
				$it = new \DirectoryIterator( $entry->getPathName() );
				$pref = ( $prefix !== '' ? $prefix . '/' : '' ) . $name;
				$subList = self::createControllers( $it, $context, $aimeos, $pref );

				$list = array_merge( $list, $subList );
			}
			else if( $prefix !== '' && $entry->getType() === 'file'
				&& ( $name = $entry->getBaseName( '.php' ) ) === 'Factory' )
			{
				$list[$prefix] = self::createController( $context, $aimeos, $prefix );
			}
		}

		return $list;
	}
}
