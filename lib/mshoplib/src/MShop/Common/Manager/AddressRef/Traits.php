<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\AddressRef;


/**
 * Common trait for managers retrieving/storing address items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Creates a new address item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Address\Iface New address item object
	 */
	public function createAddressItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Address\Iface
	{
		return $this->getObject()->getSubManager( 'address' )->create( $values );
	}


	/**
	 * Returns the address items for the given parent IDs
	 *
	 * @param string[] $parentIds List of parent IDs
	 * @param string $domain Domain of the calling manager
	 * @return array Associative list of parent IDs / address IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Address\Iface as values
	 */
	protected function getAddressItems( array $parentIds, string $domain ) : array
	{
		$list = [];

		if( !empty( $parentIds ) )
		{
			$manager = $this->getObject()->getSubManager( 'address' );

			$search = $manager->filter()->slice( 0, 0x7fffffff );
			$search->setConditions( $search->compare( '==', $domain . '.address.parentid', $parentIds ) );
			$search->setSortations( [$search->sort( '+', $domain . '.address.position' )] );

			foreach( $manager->search( $search ) as $id => $addrItem ) {
				$list[$addrItem->getParentId()][$id] = $addrItem;
			}
		}

		return $list;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function getObject() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	abstract public function getSubManager( string $domain, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Adds new, updates existing and deletes removed address items
	 *
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\AddressRef\Iface Item with saved referenced items
	 */
	protected function saveAddressItems( \Aimeos\MShop\Common\Item\AddressRef\Iface $item, string $domain,
		bool $fetch = true ) : \Aimeos\MShop\Common\Item\AddressRef\Iface
	{
		$pos = 0;
		$manager = $this->getObject()->getSubManager( 'address' );
		$manager->delete( $item->getAddressItemsDeleted()->keys()->toArray() );

		foreach( $item->getAddressItems() as $idx => $addrItem )
		{
			if( $addrItem->getParentId() != $item->getId() ) {
				$addrItem = $addrItem->setId( null ); //create new address item if copied
			}

			$addrItem = $addrItem->setParentId( $item->getId() )->setPosition( $pos++ );
			$addrItem = $manager->save( $addrItem, $fetch );
			$item = $item->addAddressItem( $addrItem, $idx );
		}

		return $item;
	}
}
