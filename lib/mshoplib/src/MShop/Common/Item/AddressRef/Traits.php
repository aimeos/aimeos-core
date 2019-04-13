<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\AddressRef;


/**
 * Common trait for items containing address items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	private $addrMax = 0;
	private $addrItems = [];
	private $addrRmItems = [];
	private $addrSorted;


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();

		foreach( $this->addrItems as $key => $item ) {
			$this->addrItems[$key] = clone $item;
		}

		foreach( $this->addrRmItems as $key => $item ) {
			$this->addrRmItems[$key] = clone $item;
		}
	}


	/**
	 * Adds a new address item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item New or existing address item
	 * @param integer|null $idx Key in the list of address items or null to add the item at the end
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item, $idx = null )
	{
		$idx !== null ? $this->addrItems[$idx] = $item : $this->addrItems[] = $item;
		return $this;
	}


	/**
	 * Removes an existing address item
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function deleteAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item )
	{
		foreach( $this->addrItems as $key => $addrItem )
		{
			if( $addrItem === $item )
			{
				$this->addrRmItems[$item->getId()] = $item;
				unset( $this->addrItems[$key] );

				return $this;
			}
		}

		return $this;
	}


	/**
	 * Removes a list of existing address items
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a address item or isn't found
	 */
	public function deleteAddressItems( array $items )
	{
		foreach( $items as $item ) {
			$this->deleteAddressItem( $item );
		}

		return $this;
	}


	/**
	 * Returns the deleted address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Address items
	 */
	public function getAddressItemsDeleted()
	{
		return $this->addrRmItems;
	}


	/**
	 * Returns the address items
	 *
	 * @param integer $idx Key in the list of address items
	 * @return \Aimeos\MShop\Common\Item\Address\Iface|null Address item or null if not found
	 */
	public function getAddressItem( $idx )
	{
		return ( isset( $this->addrItems[$idx] ) ? $this->addrItems[$idx] : null );
	}


	/**
	 * Returns the address items
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of address IDs as keys and address items as values
	 */
	public function getAddressItems()
	{
		return $this->addrItems;
	}


	/**
	 * Initializes the address items in the trait
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface[] $items Address items
	 */
	protected function initAddressItems( array $items )
	{
		$this->addrItems = $items;
	}
}
