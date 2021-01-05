<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2019-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Helper\Config;


/**
 * Generic interface for the helper config item
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Initializes the object with the criteria objects to check against
	 *
	 * @param \Aimeos\MW\Criteria\Attribute\Iface $criteria Criteria attribute objects
	 */
	public function __construct( array $criteria );

	/**
	 * Checks required fields and the types of the config array
	 *
	 * @param array $map Values to check agains the criteria
	 * @return array An array with the attribute keys as key and an error message as values for all attributes that are
	 * 	known by the provider but aren't valid resp. null for attributes whose values are OK
	 */
	public function check( array $config ) : array;
}
