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
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addAddressItem( \Aimeos\MShop\Common\Item\Address\Iface $item )
	{
		$id = $item->getId() ?: 'id-' . $this->addrMax++;
		$this->addrItems[$id] = $item;
		$this->addrSorted = null;

		return $this;
	}


	/**
	 * Removes an existing address item
	 *
	 * @param \Aimeos\MShop\Common\Item\Address\Iface $item Existing address item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If given address item isn't found
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

		throw new \Aimeos\MShop\Exception( sprintf( 'Address item for removal not found' ) );
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
	 * Returns the address items of the product
	 *
	 * @return \Aimeos\MShop\Common\Item\Address\Iface[] Associative list of address IDs as keys and address items as values
	 */
	public function getAddressItems()
	{
		if( $this->addrSorted === null )
		{
			$fcn = function( $a, $b )
			{
				if( $a->getPosition() == $b->getPosition() ) {
					return 0;
				}

				return ( $a->getPosition() < $b->getPosition() ? -1 : 1 );
			};

			uasort( $this->addrItems, $fcn );
			$this->addrSorted = true;
		}

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
