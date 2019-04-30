<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListRef;


/**
 * Trait for managers working with referenced list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Creates a new item for the specific manager.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @return \Aimeos\MShop\Common\Item\Iface New item
	 */
	abstract protected function createItemBase( array $values = [], array $listItems = [], array $refItems = [] );


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param string[]|null $domains List of domains to fetch list items and referenced items for or null for all
	 * @param string $prefix Domain prefix
	 * @param array $local Associative list of IDs as keys and the associative array of items as values
	 * @param array $local2 Associative list of IDs as keys and the associative array of items as values
	 * @return \Aimeos\MShop\Common\Item\Iface[] List of items
	 */
	protected function buildItems( array $map, $domains, $prefix, array $local = [], array $local2 = [] )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = [];

		if( $domains === null || !empty( $domains ) )
		{
			$listItems = $this->getListItems( array_keys( $map ), $domains, $prefix );

			foreach( $listItems as $listItem )
			{
				$domain = $listItem->getDomain();
				$parentid = $listItem->getParentId();

				$listItemMap[$parentid][$domain][$listItem->getId()] = $listItem;
				$refIdMap[$domain][$listItem->getRefId()][] = $parentid;
			}

			$refItemMap = $this->getRefItems( $refIdMap, $domains );
		}

		foreach( $map as $id => $values )
		{
			$listItems = ( isset( $listItemMap[$id] ) ? $listItemMap[$id] : [] );
			$refItems = ( isset( $refItemMap[$id] ) ? $refItemMap[$id] : [] );
			$localItems = ( isset( $local[$id] ) ? $local[$id] : [] );
			$localItems2 = ( isset( $local2[$id] ) ? $local2[$id] : [] );

			$items[$id] = $this->createItemBase( $values, $listItems, $refItems, $localItems, $localItems2 );
		}

		return $items;
	}


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	abstract protected function getContext();


	/**
	 * Returns the list items that belong to the given IDs.
	 *
	 * @param string[] $ids List of IDs
	 * @param string[]|null $domains List of domain names whose referenced items should be attached or null for all
	 * @param string $prefix Domain prefix
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface[] List of list items
	 */
	protected function getListItems( array $ids, $domains, $prefix )
	{
		$manager = $this->getObject()->getSubManager( 'lists' );
		$search = $manager->createSearch()->setSlice( 0, 0x7fffffff );

		if( is_array( $domains ) )
		{
			$list = [];
			$expr = [$search->compare( '==', $prefix . '.lists.parentid', $ids )];

			foreach( $domains as $key => $domain )
			{
				if( is_array( $domain ) )
				{
					$list[] = $search->combine( '&&', [
						$search->compare( '==', $prefix . '.lists.domain', $key ),
						$search->compare( '==', $prefix . '.lists.type', $domain ),
					] );
				}
				else
				{
					$list[] = $search->compare( '==', $prefix . '.lists.domain', $domain );
				}
			}

			$expr[] = $search->combine( '||', $list );
			$search->setConditions( $search->combine( '&&', $expr ) );
		}
		else
		{
			$search->setConditions( $search->compare( '==', $prefix . '.lists.parentid', $ids ) );
		}

		return $manager->searchItems( $search );
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function getObject();


	/**
	 * Returns the referenced items for the given IDs.
	 *
	 * @param array $refIdMap Associative list of domain/ref-ID/parent-item-ID key/value pairs
	 * @param string[]|null $domains List of domain names whose referenced items should be attached or null for all
	 * @return array Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function getRefItems( array $refIdMap, $domains = [] )
	{
		$items = [];

		foreach( $refIdMap as $domain => $list )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), $domain );

			$search = $manager->createSearch()->setSlice( 0, count( $list ) );
			$search->setConditions( $search->compare( '==', str_replace( '/', '.', $domain ) . '.id', array_keys( $list ) ) );

			foreach( $manager->searchItems( $search, $domains ?: [] ) as $id => $item )
			{
				foreach( $list[$id] as $parentId ) {
					$items[$parentId][$domain][$id] = $item;
				}
			}
		}

		return $items;
	}


	/**
	 * Adds new, updates existing and deletes removed list items and referenced items if available
	 *
	 * @param \Aimeos\MShop\Common\Item\ListRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param boolean $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\ListRef\Iface $item with updated referenced items
	 */
	protected function saveListItems( \Aimeos\MShop\Common\Item\ListRef\Iface $item, $domain, $fetch = true )
	{
		$context = $this->getContext();
		$rmListIds = $rmIds = $refManager = [];
		$listManager = $this->getObject()->getSubManager( 'lists' );


		foreach( $item->getListItemsDeleted() as $listItem )
		{
			$rmListIds[] = $listItem->getId();

			if( ( $refItem = $listItem->getRefItem() ) !== null ) {
				$rmIds[$listItem->getDomain()][] = $refItem->getId();
			}
		}


		try
		{
			foreach( $rmIds as $refDomain => $ids )
			{
				$refManager[$refDomain] = \Aimeos\MShop::create( $context, $refDomain );
				$refManager[$refDomain]->begin();

				$refManager[$refDomain]->deleteItems( $ids );
			}

			$listManager->deleteItems( $rmListIds );


			foreach( $item->getListItems( null, null, null, false ) as $listItem )
			{
				$refDomain = $listItem->getDomain();

				if( ( $refItem = $listItem->getRefItem() ) !== null )
				{
					if( !isset( $refManager[$refDomain] ) )
					{
						$refManager[$refDomain] = \Aimeos\MShop::create( $context, $refDomain );
						$refManager[$refDomain]->begin();
					}

					$refItem = $refManager[$refDomain]->saveItem( $refItem );
					$listItem->setRefId( $refItem->getId() );
				}

				if( $listItem->getParentId() != $item->getId() ) {
					$listItem->setId( null ); // create new list item if copied
				}

				$listManager->saveItem( $listItem->setParentId( $item->getId() ), $fetch );
				// @todo update list item in $item
			}


			foreach( $refManager as $manager ) {
				$manager->commit();
			}
		}
		catch( \Exception $e )
		{
			foreach( $refManager as $manager ) {
				$manager->rollback();
			}

			throw $e;
		}

		return $item;
	}
}
