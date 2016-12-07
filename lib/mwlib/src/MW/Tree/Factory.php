<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2016
 * @package MW
 * @subpackage Tree
 */


namespace Aimeos\MW\Tree;


/**
 * Creates new instances of classes in the tree domain.
 *
 * @package MW
 * @subpackage Tree
 */
class Factory
{
	/**
	 * Creates and returns a tree manager.
	 *
	 * @param string $name Manager type name
	 * @param array $config Associative list of configuration strings for managing the tree
	 * @param \Aimeos\MW\DB\Manager\Iface|null $resource Reference to the resource which should be used for managing the tree
	 * @return \Aimeos\MW\Tree\Manager\Iface Tree manager object of the requested type
	 * @throws \Aimeos\MW\Tree\Exception if class isn't found
	 */
	static public function createManager( $name, array $config, $resource )
	{
		if( ctype_alnum( $name ) === false )
		{
			$classname = is_string( $name ) ? '\\Aimeos\\MW\\Tree\\Manager\\' . $name : '<not a string>';
			throw new \Aimeos\MW\Tree\Exception( sprintf( 'Invalid characters in class name "%1$s"', $classname ) );
		}

		$iface = '\\Aimeos\\MW\\Tree\\Manager\\Iface';
		$classname = '\\Aimeos\\MW\\Tree\\Manager\\' . $name;

		if( class_exists( $classname ) === false ) {
			throw new \Aimeos\MW\Tree\Exception( sprintf( 'Class "%1$s" not available', $classname ) );
		}

		$manager =  new $classname( $config, $resource );

		if( !( $manager instanceof $iface ) ) {
			throw new \Aimeos\MW\Tree\Exception( sprintf( 'Class "%1$s" does not implement interface "%2$s"', $classname, $iface ) );
		}

		return $manager;
	}
}
