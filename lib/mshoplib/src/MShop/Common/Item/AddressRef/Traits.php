<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	 * @param string|null $key Key in the list of address items or null to add the item at the end
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item, string $key = null ) : \Aimeos\MShop\Common\Item\Iface
	{
		$key !== null ? $this->addrItems[$key] = $item : $this->addrItems[] = $item;
		return $this;
	}


	/**
	 * Removes an existing address item
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function deleteAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item ) : \Aimeos\MShop\Common\Item\Iface
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
	 * @param \Aimeos\Map|\Aimeos\MShop\Common\Item\Address\Iface[] $items Existing address items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a address item or isn't found
	 */
	public function deleteAddressItems( iterable $items ) : \Aimeos\MShop\Common\Item\Iface
	{
		foreach( $items as $item ) {
			$this->deleteAddressItem( $item );
		}

		return $this;
	}


	/**
	 * Returns the deleted address items
	 *
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function getAddressItemsDeleted() : \Aimeos\Map
	{
		return map( $this->addrRmItems );
	}


	/**
	 * Returns the address items
	 *
	 * @param string $key Key in the list of address items
	 * @return \Aimeos\MShop\Common\Item\Address\Iface|null Address item or null if not found
	 */
	public function getAddressItem( string $key ) : ?\Aimeos\MShop\Common\Item\Iface
	{
		return $this->addrItems[$key] ?? null;
	}


	/**
	 * Returns the address items
	 *
	 * @return \Aimeos\Map List of IDs as keys and items implementing \Aimeos\MShop\Common\Item\Address\Iface
	 */
	public function getAddressItems() : \Aimeos\Map
	{
		return map( $this->addrItems );
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
