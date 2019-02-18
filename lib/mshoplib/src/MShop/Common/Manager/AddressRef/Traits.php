<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
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
	 * Returns the address items for the given parent IDs
	 *
	 * @param string[] $parentIds List of parent IDs
	 * @param string $domain Domain of the calling manager
	 * @return array Associative list of parent IDs / address IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Address\Iface as values
	 */
	protected function getAddressItems( array $parentIds, $domain )
	{
		$list = [];

		if( !empty( $parentIds ) )
		{
			$manager = $this->getObject()->getSubManager( 'address' );

			$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );
			$search->setConditions( $search->compare( '==', $domain . '.address.parentid', $parentIds ) );
			$search->setSortations( [$search->sort( '+', $domain . '.address.position')] );

			foreach( $manager->searchItems( $search ) as $addrItem ) {
				$list[$addrItem->getParentId()][] = $addrItem;
			}
		}

		return $list;
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function getObject();


	/**
	 * Adds new, updates existing and deletes removed address items
	 *
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\AddressRef\Iface Item with saved referenced items
	 */
	protected function saveAddressItems( \Aimeos\MShop\Common\Item\AddressRef\Iface $item, $domain, $fetch = true )
	{
		$manager = $this->getObject()->getSubManager( 'address' );
		$manager->deleteItems( array_keys( $item->getAddressItemsDeleted() ) );

		foreach( $item->getAddressItems() as $pos => $addrItem )
		{
			if( $addrItem->getParentId() != $item->getId() ) {
				$addrItem = $addrItem->setId( null ); //create new address item if copied
			}

			$manager->saveItem( $addrItem->setParentId( $item->getId() )->setPosition( $pos ), $fetch );
		}

		return $item;
	}
}
