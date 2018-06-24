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
	 * @param array $parentIds List of parent IDs
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

			$search = $manager->createSearch();
			$search->setConditions( $search->compare( '==', $domain . '.address.parentid', $parentIds ) );
			$search->setSlice( 0, 0x7fffffff );

			foreach( $manager->searchItems( $search ) as $id => $addrItem ) {
				$list[$addrItem->getParentId()][$id] = $addrItem;
			}
		}

		return $list;
	}


	/**
	 * Adds new, updates existing and deletes removed address items
	 *
	 * @param \Aimeos\MShop\Common\Item\AddressRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @return \Aimeos\MShop\Common\Item\AddressRef\Iface Item with saved referenced items
	 */
	protected function saveAddressItems( \Aimeos\MShop\Common\Item\AddressRef\Iface $item, $domain )
	{
		$manager = $this->getObject()->getSubManager( 'address' );
		$manager->deleteItems( array_keys( $item->getAddressItemsDeleted() ) );

		foreach( $item->getAddressItems() as $addrItem )
		{
			if( $addrItem->getParentId() != $item->getId() ) {
				$addrItem->setId( null ); //create new address item if copied
			}

			$addrItem->setParentId( $item->getId() );
			$manager->saveItem( $addrItem );
		}

		return $item;
	}
}
