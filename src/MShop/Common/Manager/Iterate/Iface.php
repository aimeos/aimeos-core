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
	 * Creates a new cursor based on the filter criteria
	 *
	 * @param \Aimeos\Base\Criteria\Iface $filter Criteria object with conditions, sortations, etc.
	 * @return \Aimeos\MShop\Common\Cursor\Iface Cursor object
	 */
	public function cursor( \Aimeos\Base\Criteria\Iface $filter ) : \Aimeos\MShop\Common\Cursor\Iface;

	/**
	 * Iterates over all matched items and returns the found ones
	 *
	 * @param \Aimeos\MShop\Common\Cursor\Iface $cursor Cursor object with filter, domains and cursor
	 * @param string[] $ref List of domains whose items should be fetched too
	 * @return \Aimeos\Map|null List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	public function iterate( \Aimeos\MShop\Common\Cursor\Iface $cursor, array $ref = [] ) : ?\Aimeos\Map;
}
