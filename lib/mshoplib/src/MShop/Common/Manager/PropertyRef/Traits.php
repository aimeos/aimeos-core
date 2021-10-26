<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
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
	public function createPropertyItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Property\Iface
	{
		return $this->getObject()->getSubManager( 'property' )->create( $values );
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
	 * Returns the property items for the given parent IDs
	 *
	 * @param string[] $parentIds List of parent IDs
	 * @param string $domain Domain of the calling manager
	 * @param array|null $types Types names of the properties that should be fetched
	 * @return array Associative list of parent IDs / property IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Property\Iface as values
	 */
	protected function getPropertyItems( array $parentIds, string $domain, array $types = null ) : array
	{
		$list = [];

		if( !empty( $parentIds ) )
		{
			$manager = $this->getObject()->getSubManager( 'property' );

			$search = $manager->filter()->slice( 0, 0x7fffffff );
			$search->setConditions( $search->compare( '==', $domain . '.property.parentid', $parentIds ) );

			if( $types !== null )
			{
				$search->setConditions( $search->and( [
					$search->compare( '==', $domain . '.property.type', $types ),
					$search->getConditions()
				] ) );
			}

			foreach( $manager->search( $search ) as $id => $propItem ) {
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
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\PropertyRef\Iface Item with saved referenced items
	 */
	protected function savePropertyItems( \Aimeos\MShop\Common\Item\PropertyRef\Iface $item, string $domain,
		bool $fetch = true ) : \Aimeos\MShop\Common\Item\PropertyRef\Iface
	{
		$propManager = $this->getObject()->getSubManager( 'property' );
		$propManager->delete( $item->getPropertyItemsDeleted()->keys()->toArray() );

		$propItems = $item->getPropertyItems( null, false );

		foreach( $propItems as $propItem )
		{
			if( $propItem->getParentId() != $item->getId() ) {
				$propItem->setId( null ); // create new property item if copied
			}

			$propItem->setParentId( $item->getId() );
		}

		$propManager->save( $propItems, $fetch );
		return $item;
	}
}
