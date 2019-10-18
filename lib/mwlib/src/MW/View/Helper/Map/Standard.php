<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019
 * @package MW
 * @subpackage View
 */


namespace Aimeos\MW\View\Helper\Map;


/**
 * View helper class for mapping arrays/objects
 *
 * @package MW
 * @subpackage View
 */
class Standard implements Iface
{
	/**
	 * Returns the mapped array
	 *
	 * @param iterable $cfgkey List of arrays of object that should be mapped
	 * @param array $key Name of the property whose value should be the key of the mapped pairs
	 * @param string $prop Property name that should be mapped to the key
	 * @return \Aimeos\MW\MapIface Associative list of key/value pairs
	 */
	public function transform( iterable $list, string $key, string $prop ) : \Aimeos\MW\MapIface
	{
		$result = [];

		foreach( $list as $entry )
		{
			if( is_object( $entry ) && method_exists( $entry, 'toArray' ) ) {
				$entry = $entry->toArray();
			}

			if( array_key_exists( $key, $entry ) && array_key_exists( $prop, $entry ) ) {
				$result[$entry[$key]] = $entry[$prop];
			}
		}

		return \Aimeos\MW\Map::from( $result );
	}
}
