<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2012
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base\Coupon;


/**
 * Generic interface for order base coupon managers.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Adds a new item to the storage or updates an existing one.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Coupon\Iface $item Item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Coupon\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Coupon\Iface $item, $fetch = true );
}
