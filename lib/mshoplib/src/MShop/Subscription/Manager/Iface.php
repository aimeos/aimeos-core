<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2020
 * @package MShop
 * @subpackage Subscription
 */


namespace Aimeos\MShop\Subscription\Manager;


/**
 * Generic interface for subscription manager implementations
 *
 * @package MShop
 * @subpackage Subscription
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a one-time subscription in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Subscription\Item\Iface $item Subscription item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Subscription\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Subscription\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Subscription\Item\Iface;
}
