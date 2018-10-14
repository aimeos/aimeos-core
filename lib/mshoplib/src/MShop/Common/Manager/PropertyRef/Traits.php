<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\PropertyRef;


/**
 * Common trait for managers retrieving/storing property items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Returns the property items for the given parent IDs
	 *
	 * @param array $parentIds List of parent IDs
	 * @param string $domain Domain of the calling manager
	 * @return array Associative list of parent IDs / property IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Property\Iface as values
	 */
	protected function getPropertyItems( array $parentIds, $domain )
	{
		$list = [];

		if( !empty( $parentIds ) )
		{
			$propManager = $this->getObject()->getSubManager( 'property' );

			$propSearch = $propManager->createSearch()->setSlice( 0, 0x7fffffff );
			$propSearch->setConditions( $propSearch->compare( '==', $domain . '.property.parentid', $parentIds ) );
			$propSearch->setSortations( [$propSearch->sort( '+', $domain . '.property.type.position' )] );

			foreach( $propManager->searchItems( $propSearch ) as $id => $propItem ) {
				$list[$propItem->getParentId()][$id] = $propItem;
			}
		}

		return $list;
	}


	/**
	 * Adds new, updates existing and deletes removed property items
	 *
	 * @param \Aimeos\MShop\Common\Item\PropertyRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Item with saved referenced items
	 */
	protected function savePropertyItems( \Aimeos\MShop\Common\Item\PropertyRef\Iface $item, $domain, $fetch = true )
	{
		$propManager = $this->getObject()->getSubManager( 'property' );
		$propManager->deleteItems( array_keys( $item->getPropertyItemsDeleted() ) );

		foreach( $item->getPropertyItems( null, false ) as $propItem )
		{
			if( $propItem->getParentId() != $item->getId() ) {
				$propItem->setId( null ); // create new property item if copied
			}

			$propItem->setParentId( $item->getId() );
			$propManager->saveItem( $propItem, $fetch );
		}

		return $item;
	}
}
