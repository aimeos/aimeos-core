<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2023
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
	 * @param \Aimeos\Base\DB\Connection\Iface|null $resource Reference to the resource which should be used for managing the tree
	 * @return \Aimeos\MW\Tree\Manager\Iface Tree manager object of the requested type
	 * @throws \LogicException If class isn't found
	 */
	public static function create( string $name, array $config, $resource )
	{
		if( ctype_alnum( $name ) === false ) {
			throw new \LogicException( sprintf( 'Invalid characters in class name "%1$s"', $name ), 400 );
		}

		$interface = \Aimeos\MW\Tree\Manager\Iface::class;
		$classname = '\Aimeos\MW\Tree\Manager\\' . $name;

		return \Aimeos\Utils::create( $classname, [$config, $resource], $interface );
	}
}
