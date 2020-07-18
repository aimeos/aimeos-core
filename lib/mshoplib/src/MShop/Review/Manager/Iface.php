<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2020
 * @package MShop
 * @subpackage Review
 */


namespace Aimeos\MShop\Review\Manager;


/**
 * Generic interface for review manager implementations
 *
 * @package MShop
 * @subpackage Review
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a one-time review in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Review\Item\Iface $item Review item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Review\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Review\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Review\Item\Iface;
}
