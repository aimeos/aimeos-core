<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
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
interface Iface extends \Aimeos\MW\View\Helper\Iface
{
	/**
	 * Returns the mapped array
	 *
	 * @param iterable $cfgkey List of arrays of object that should be mapped
	 * @param string $key Name of the property whose value should be the key of the mapped pairs
	 * @param string $prop Property name or names that should be mapped to the key
	 * @return \Aimeos\Map Associative list of key/value pairs
	 */
	public function transform( iterable $list, string $key, string $prop ) : \Aimeos\Map;
}
