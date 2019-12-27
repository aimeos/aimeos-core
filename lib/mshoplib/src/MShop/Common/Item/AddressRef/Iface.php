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
interface Iface extends \Aimeos\MShop\Common\Item\Iface
{
	/**
	 * Adds a new address item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item New or existing address item
	 * @param int|null $pos Position (key) in the list of address items or null to add the item at the end
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item, int $pos = null ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Removes an existing address item
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function deleteAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Removes a list of existing address items
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function deleteAddressItems( array $items ) : \Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the deleted address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Address items
	 */
	public function getAddressItemsDeleted() : array;

	/**
	 * Returns the address items
	 *
	 * @param int $idx Position (key) in the list of address items
	 * @return \Aimeos\MShop\Common\Item\Address\Iface|null Address item or null if not found
	 */
	public function getAddressItem( int $idx ) : ?\Aimeos\MShop\Common\Item\Iface;

	/**
	 * Returns the address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of address IDs as keys and address items as values
	 */
	public function getAddressItems() : array;
}
