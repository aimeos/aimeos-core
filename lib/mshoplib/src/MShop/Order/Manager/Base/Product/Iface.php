<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Order
 */


namespace Aimeos\MShop\Order\Manager\Base\Product;


/**
 * Generic interface for order base product managers.
 *
 * @package MShop
 * @subpackage Order
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Adds or updates a order base product item to the storage.
	 *
	 * @param \Aimeos\MShop\Order\Item\Base\Product\Iface $item New or existing product item that should be saved to the storage
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Order\Item\Base\Product\Iface $item Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Order\Item\Base\Product\Iface $item, $fetch = true );
}
