<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\AddressRef;


/**
 * Common interface for items containing address items.
 *
 * @package MShop
 * @subpackage Common
 */
interface Iface
{
	/**
	 * Adds a new address item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item New or existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item );

	/**
	 * Removes an existing address item
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If given address item isn't found
	 */
	public function deleteAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item );

	/**
	 * Removes a list of existing address items
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If a address item isn't found
	 */
	public function deleteAddressItems( array $items );

	/**
	 * Returns the deleted address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Address items
	 */
	public function getAddressItemsDeleted();

	/**
	 * Returns the address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of address IDs as keys and address items as values
	 */
	public function getAddressItems();
}
