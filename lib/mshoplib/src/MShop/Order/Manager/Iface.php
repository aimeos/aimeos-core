<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2020
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager;


/**
 * Generic interface for order manager implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a one-time order in the storage from the given invoice object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Iface $item Order item with necessary values
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Iface;
}
