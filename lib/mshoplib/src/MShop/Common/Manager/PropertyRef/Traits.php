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
	 * Creates a new property item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function createPropertyItem( array $values = [] )
	{
		return $this->getObject()->getSubManager( 'property' )->createItem( $values );
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function getObject();


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	abstract public function getSubManager( $domain, $name = null );


	/**
	 * Returns the property items for the given parent IDs
	 *
	 * @param string[] $parentIds List of parent IDs
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
