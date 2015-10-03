<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2011
 * @license LGPLv3, http://www.gnu.org/licenses/lgpl.html
 * @package MW
 * @subpackage Tree
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
	 * @param string $name Manager type name
	 * @param array $config Associative list of configuration strings for managing the tree
	 * @param MW_DB_Manager_Iface|null $resource Reference to the resource which should be used for managing the tree
	 * @return MW_Tree_Manager_Iface Tree manager object of the requested type
	 * @throws MW_Tree_Exception if class isn't found
	 */
	static public function createManager( $name, array $config, $resource )
	{
		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? 'MW_Tree_Manager_' . $name : '<not a string>';
			throw new MW_Tree_Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = 'MW_Tree_Manager_Iface';
		$classname = 'MW_Tree_Manager_' . $name;

		if( class_exists( $classname ) === false ) {
			throw new MW_Tree_Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$manager =  new $classname( $config, $resource );

		if( !( $manager instanceof $iface ) ) {
			throw new MW_Tree_Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $manager;
	}
}
