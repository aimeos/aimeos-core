<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2022
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\Iterate;


/**
 * Common interface for managers which support iterating over a result set
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Creates a new iterator based on the filter criteria
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @return \Aimeos\MShop\Common\Iterator\Iface Iterator object
	 */
	public function iterator( \Aimeos\Base\Criteria\Iface $filter ) : \Aimeos\MShop\Common\Iterator\Iface;

	/**
	 * Iterates over all matching items and returns the found ones
	 *
	 * @param \Aimeos\MShop\Common\Iterator\Iface $iterator Iterator object with conditions, sortations, etc.
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int $count Maximum number of items which should be returned
	 * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function iterate( \Aimeos\MShop\Common\Iterator\Iface $iterator, array $ref = [], int $count = 100 ) : ?\Aimeos\Map;
}
