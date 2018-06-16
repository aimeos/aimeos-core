<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2017
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListRef;


/**
 * Abstract class for managers working with referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class Base
	extends \Aimeos\MShop\Common\Manager\Base
	implements \Aimeos\MShop\Common\Manager\ListRef\Iface
{
	private $typeIds = [];

	/**
	 * Updates the list items to the referenced items for the given domain and type
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item including references to other domain items
	 * @param array $map Associative list of reference ID as key and list of list item properties as key/value pairs
	 * @param string $domain Domain name of the referenced items
	 * @param string $type List type for the referenced items
	 */
	public function updateListItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, array $map, $domain, $type )
	{
		$listManager =  $this->getObject()->getSubManager( 'lists' );

		if( !isset( $this->typeIds[$domain][$type] ) )
		{
			$typeManager = $listManager->getSubManager( 'type' );
			$this->typeIds[$domain][$type] = $typeManager->findItem( $type, [], $domain )->getId();
		}

		$listItem = $listManager->createItem();
		$listItem->setTypeId( $this->typeIds[$domain][$type] );
		$listItem->setParentId( $item->getId() );
		$listItem->setDomain( $domain );
		$listItem->setStatus( 1 );

		$pos = 0;
		$listRef = $ids = [];
		$listItems = $item->getListItems( $domain, $type );

		foreach( $listItems as $id => $listItem ) {
			$listRef[$listItem->getRefId()] = $id;
		}

		foreach( $map as $id => $values )
		{
			$copy = $listItem;

			if( !isset( $listRef[$id] ) )
			{
				$copy->setId( null );
				$copy->setRefId( $id );
			}
			else
			{
				$copy = $listItems[$listRef[$id]];
				$ids[] = $listRef[$id];
			}

			if( is_array( $values ) ) {
				$copy->fromArray( $values );
			}

			$copy->setPosition( $pos++ );
			$listManager->saveItem( $copy, false );
		}

		$listManager->deleteItems( array_diff( $listRef, $ids ) );
	}


	/**
	 * Creates a new item for the specific manager.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface
	 * @param array $refItems List of items implementing \Aimeos\MShop\Common\Item\Iface
	 * @return \Aimeos\MShop\Common\Item\Iface New item
	 */
	abstract protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] );


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param array|null $domains List of domains to fetch list items and referenced items for or null for all
	 * @param string $prefix Domain prefix
	 * @param array $local Associative list of IDs as keys and the associative array of items as values
	 * @param array $local2 Associative list of IDs as keys and the associative array of items as values
	 * @return array List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	protected function buildItems( array $map, $domains, $prefix, array $local = [], array $local2 = [] )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = [];

		if( $domains === null || count( $domains ) > 0 )
		{
			$listItems = $this->getListItems( array_keys( $map ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->getRefItems( $refIdMap );
		}

		foreach( $map as $id => $values )
		{
			$listItems = [];
			if( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = [];
			if( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$localItems = [];
			if( isset( $local[$id] ) ) {
				$localItems = $local[$id];
			}

			$localItems2 = [];
			if( isset( $local2[$id] ) ) {
				$localItems2 = $local2[$id];
			}

			$items[$id] = $this->createItemBase( $values, $listItems, $refItems, $localItems, $localItems2 );
		}

		return $items;
	}


	/**
	 * Returns the list items that belong to the given IDs.
	 *
	 * @param array $ids List of IDs
	 * @param array|null $domains List of domain names whose referenced items should be attached or null for all
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing \Aimeos\MShop\Common\Lists\Item\Iface
	 */
	protected function getListItems( array $ids, $domains, $prefix )
	{
		$manager = $this->getObject()->getSubManager( 'lists' );

		$search = $manager->createSearch();

		$expr = [ $search->compare( '==', $prefix . '.lists.parentid', $ids ) ];

		if( $domains !== null ) {
			$expr[] = $search->compare( '==', $prefix . '.lists.domain', $domains );
		}

		$search->setConditions( $search->combine( '&&', $expr ) );
		$search->setSlice( 0, 0x7fffffff );

		return $manager->searchItems( $search );
	}


	/**
	 * Returns the referenced items for the given IDs.
	 *
	 * @param array $refIdMap Associative list of domain/ref-ID/parent-item-ID key/value pairs
	 * @return array Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function getRefItems( array $refIdMap )
	{
		$items = [];
		$context = $this->getContext();

		foreach( $refIdMap as $domain => $list )
		{
			try
			{
				$manager = \Aimeos\MShop\Factory::createManager( $context, $domain );

				$search = $manager->createSearch();
				$search->setConditions( $search->compare( '==', str_replace( '/', '.', $domain ) . '.id', array_keys( $list ) ) );
				$search->setSlice( 0, 0x7fffffff );

				foreach( $manager->searchItems( $search ) as $id => $item )
				{
					foreach( $list[$id] as $parentId ) {
						$items[$parentId][$domain][$id] = $item;
					}
				}
			}
			catch( \Aimeos\MShop\Exception $e )
			{
				$logger = $context->getLogger();
				$logger->log( sprintf( 'Item referenced in domain "%1$s" not found: %2$s', $domain, $e->getMessage() ) );
				$logger->log( $e->getTraceAsString() );
			}
		}

		return $items;
	}


	/**
	 * Adds new, updates existing and deletes removed referenced items
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface $item with updated referenced items
	 */
	protected function saveRefItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, $domain )
	{
		$rmListIds = $rmIds = [];
		$context = $this->getContext();
		$listManager = \Aimeos\MShop\Factory::createManager( $context, $domain . '/lists' );

		foreach( $item->getDeletedItems() as $listItem )
		{
			$rmListIds[] = $listItem->getId();

			if( ( $refItem = $listItem->getRefItem() ) !== null ) {
				$rmIds[$listItem->getDomain()][] = $refItem->getId();
			}
		}

		foreach( $rmIds as $refDomain => $ids ) {
			\Aimeos\MShop\Factory::createManager( $context, $refDomain )->deleteItems( $ids );
		}

		$listManager->deleteItems( $rmListIds );

		foreach( $item->getListItems( null, null, null, false ) as $listItem )
		{
			if( ( $refItem = $listItem->getRefItem() ) !== null )
			{
				if( $refItem instanceof \Aimeos\MShop\Common\Item\Domain\Iface ) {
					$refItem->setDomain( $domain );
				}

				$manager = \Aimeos\MShop\Factory::createManager( $context, $listItem->getDomain() );
				$refItem = $manager->saveItem( $refItem );

				$listItem->setRefId( $refItem->getId() );
			}

			if( $listItem->getParentId() != $item->getId() ) {
				$listItem->setId( null ); //create new list item if copied
			}

			$listItem->setParentId( $item->getId() );
			$listManager->saveItem( $listItem );
		}

		return $item;
	}
}
