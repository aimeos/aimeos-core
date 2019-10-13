<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2011
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Supplier
 */


namespace Aimeos\MShop\Supplier\Manager;


/**
 * Interface for supplier DAOs used by the shop.
 * @package MShop
 * @subpackage Supplier
 */
interface Iface
	extends \Aimeos\MShop\Common\Manager\Iface, \Aimeos\MShop\Common\Manager\Find\Iface,
		\Aimeos\MShop\Common\Manager\AddressRef\Iface, \Aimeos\MShop\Common\Manager\ListRef\Iface
{
	/**
	 * Saves a supplier item object.
	 *
	 * @param \Aimeos\MShop\Supplier\Item\Iface $item Supplier item object
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Supplier\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Supplier\Item\Iface $item, $fetch = true );
}
