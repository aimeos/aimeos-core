<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2024
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
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function object() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Creates a new property item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Property\Iface New property item object
	 */
	public function createPropertyItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Property\Iface
	{
		return $this->object()->getSubManager( 'property' )->create( $values );
	}


	/**
	 * Returns the property items for the given parent IDs
	 *
	 * @param string[] $parentIds List of parent IDs
	 * @param string $domain Domain of the calling manager
	 * @param array|null $ref Referenced items that should be fetched too
	 * @return array Associative list of parent IDs / property IDs as keys and items implementing
	 * 	\Aimeos\MShop\Common\Item\Property\Iface as values
	 */
	protected function getPropertyItems( array $parentIds, string $domain, ?array $ref = [] ) : array
	{
		if( empty( $parentIds ) ) {
			return [];
		}

		$manager = $this->object()->getSubManager( 'property' );
		$filter = $manager->filter()->slice( 0, 0x7fffffff )->add( $domain . '.property.parentid', '==', $parentIds );

		$name = $domain . '/property';
		$types = $ref && isset( $ref[$name] ) && is_array( $ref[$name] ) ? $ref[$name] : null;

		if( !empty( $types ) ) {
			$filter->add( $domain . '.property.type', '==', $types );
		}

		return $manager->search( $filter, $ref ?? [] )->groupBy( $domain . '.property.parentid' )->all();
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
		$propManager = $this->object()->getSubManager( 'property' );
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
