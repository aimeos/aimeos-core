<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
 * @version $Id: Factory.php 16606 2012-10-19 12:50:23Z nsendetzky $
 */


/**
 * Creates new instances of classes in the tree domain.
 *
 * @package MW
 * @subpackage Tree
 */
class MW_Tree_Factory
{
	/**
	 * Creates and returns a tree manager.
	 *
	 * @param string $type Manager type name
	 * @param array $config Associative list of configuration strings for managing the tree
	 * @param mixed $resource Reference to the resource which should be used for managing the tree
	 * @return MW_Tree_Manager_Interface Tree manager object of the requested type
	 * @throws MW_Tree_Exception if class isn't found
	 */
	static public function createManager( $type, array $config, $resource )
	{
		$classname = 'MW_Tree_Manager_' . $type;
		$filename = str_replace( '_', '/', $classname ) . '.php';

		$paths = explode( PATH_SEPARATOR, get_include_path() );

		foreach( $paths as $path )
		{
			$file = $path . DIRECTORY_SEPARATOR . $filename;
			if( file_exists( $file ) === true  && ( include_once $file ) !== false && class_exists($classname) ) {
				return new $classname( $config, $resource );
			}
		}

		throw new MW_Tree_Exception( sprintf( 'Tree manager "%1$s" not found', $type ) );
	}
}
