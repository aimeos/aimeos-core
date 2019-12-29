<?php
/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */

namespace Aimeos\MShop\Order\Manager\Status;


/**
 * Interface for all order status manager implementations.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Adds or updates an order status object.
	 *
	 * @param \Aimeos\MShop\Order\Item\Status\Iface $item Order status object whose data should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Status\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Status\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Order\Item\Status\Iface;
}
