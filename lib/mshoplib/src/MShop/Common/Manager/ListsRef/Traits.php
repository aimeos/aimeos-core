<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2021
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Manager\ListsRef;


/**
 * Trait for managers working with referenced list items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	/**
	 * Creates a new lists item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Common\Item\Lists\Iface New list items object
	 */
	public function createListItem( array $values = [] ) : \Aimeos\MShop\Common\Item\Lists\Iface
	{
		return $this->getObject()->getSubManager( 'lists' )->create( $values );
	}


	/**
	 * Creates a new item for the specific manager.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param \Aimeos\MShop\Common\Item\Lists\Iface[] $listItems List of list items
	 * @param \Aimeos\MShop\Common\Item\Iface[] $refItems List of referenced items
	 * @return \Aimeos\MShop\Common\Item\Iface New item
	 */
	abstract protected function createItemBase( array $values = [], array $listItems = [],
		array $refItems = [] ) : \Aimeos\MShop\Common\Item\Iface;


	/**
	 * Applies the filters for the item type to the item
	 *
	 * @param object $item Item to apply the filter to
	 * @return object|null Object if the item should be used, null if not
	 */
	abstract protected function applyFilter( $item );


	/**
	 * Returns the context object.
	 *
	 * @return \Aimeos\MShop\Context\Item\Iface Context object
	 */
	abstract protected function getContext() : \Aimeos\MShop\Context\Item\Iface;


	/**
	 * Creates a new extension manager in the domain.
	 *
	 * @param string $domain Name of the domain (product, text, media, etc.)
	 * @param string|null $name Name of the implementation, will be from configuration (or Standard) if null
	 * @return \Aimeos\MShop\Common\Manager\Iface Manager extending the domain functionality
	 */
	abstract public function getSubManager( string $domain, string $name = null ) : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param string[] $domains List of domains to fetch list items and referenced items for
	 * @param string $prefix Domain prefix
	 * @param array $local Associative list of IDs as keys and the associative array of items as values
	 * @param array $local2 Associative list of IDs as keys and the associative array of items as values
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface with ids as keys
	 */
	protected function buildItems( array $map, array $domains, string $prefix, array $local = [], array $local2 = [] ) : \Aimeos\Map
	{
		$items = $listItemMap = $refItemMap = [];

		foreach( $this->getListItems( array_keys( $map ), $domains, $prefix ) as $id => $listItem )
		{
			$domain = $listItem->getDomain();
			$parentid = $listItem->getParentId();

			$listItemMap[$parentid][$domain][$id] = $listItem;

			if( $refItem = $listItem->getRefItem() ) {
				$refItemMap[$parentid][$domain][$listItem->getRefId()] = $refItem;
			}
		}

		foreach( $map as $id => $values )
		{
			$localItems = $local[$id] ?? [];
			$localItems2 = $local2[$id] ?? [];
			$refItems = $refItemMap[$id] ?? [];
			$listItems = $listItemMap[$id] ?? [];

			if( $item = $this->applyFilter( $this->createItemBase( $values, $listItems, $refItems, $localItems, $localItems2 ) ) ) {
				$items[$id] = $item;
			}
		}

		return map( $items );
	}


	/**
	 * Removes the items referenced by the given list items.
	 *
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface[]|\Aimeos\Map|array $items List of items with deleted list items
	 * @return \Aimeos\MShop\Common\Manager\ListsRef\Iface Manager object for method chaining
	 */
	protected function deleteRefItems( $items ) : \Aimeos\MShop\Common\Manager\ListsRef\Iface
	{
		if( is_map( $items ) ) { $items = $items->toArray(); }
		if( !is_array( $items ) ) { $items = [$items]; }
		if( empty( $items ) ) { return $this; }

		$map = [];

		foreach( $items as $item )
		{
			if( $item instanceof \Aimeos\MShop\Common\Item\ListsRef\Iface )
			{
				foreach( $item->getListItemsDeleted() as $listItem )
				{
					if( $listItem->getRefItem() ) {
						$map[$listItem->getDomain()][] = $listItem->getRefId();
					}
				}
			}
		}

		foreach( $map as $domain => $ids ) {
			\Aimeos\MShop::create( $this->getContext(), $domain )->begin()->delete( $ids )->commit();
		}

		return $this;
	}


	/**
	 * Returns the list items that belong to the given IDs.
	 *
	 * @param string[] $ids List of IDs
	 * @param string[] $domains List of domain names whose referenced items should be attached
	 * @param string $prefix Domain prefix
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Lists\Iface with IDs as keys
	 */
	protected function getListItems( array $ids, array $domains, string $prefix ) : \Aimeos\Map
	{
		if( empty( $domains ) ) {
			return map();
		}

		$manager = $this->getObject()->getSubManager( 'lists' );
		$search = $manager->filter()->slice( 0, 0x7fffffff )->order( [
			$prefix . '.lists.parentid',
			$prefix . '.lists.domain',
			$prefix . '.lists.siteid',
			$prefix . '.lists.type',
			$prefix . '.lists.refid'
		] );

		if( is_array( $domains ) )
		{
			$list = [];
			$expr = [$search->compare( '==', $prefix . '.lists.parentid', $ids )];

			foreach( $domains as $key => $domain )
			{
				if( is_array( $domain ) )
				{
					$list[] = $search->and( [
						$search->compare( '==', $prefix . '.lists.domain', $key ),
						$search->compare( '==', $prefix . '.lists.type', $domain ),
					] );
				}
				else
				{
					$list[] = $search->compare( '==', $prefix . '.lists.domain', $domain );
				}
			}

			$expr[] = $search->or( $list );
			$search->setConditions( $search->and( $expr ) );
		}
		else
		{
			$search->setConditions( $search->compare( '==', $prefix . '.lists.parentid', $ids ) );
		}

		return $manager->search( $search, $domains );
	}


	/**
	 * Returns the outmost decorator of the decorator stack
	 *
	 * @return \Aimeos\MShop\Common\Manager\Iface Outmost decorator object
	 */
	abstract protected function getObject() : \Aimeos\MShop\Common\Manager\Iface;


	/**
	 * Returns the referenced items for the given IDs.
	 *
	 * @param array $refIdMap Associative list of domain/ref-ID/parent-item-ID key/value pairs
	 * @param string[] $domains List of domain names whose referenced items should be attached
	 * @return array Associative list of parent-item-ID/domain/items key/value pairs
	 */
	protected function getRefItems( array $refIdMap, array $domains ) : array
	{
		$items = [];

		foreach( $refIdMap as $domain => $list )
		{
			$manager = \Aimeos\MShop::create( $this->getContext(), $domain );

			$search = $manager->filter()->slice( 0, count( $list ) );
			$search->setConditions( $search->compare( '==', str_replace( '/', '.', $domain ) . '.id', array_keys( $list ) ) );

			foreach( $manager->search( $search, $domains ) as $id => $item )
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
	 * @param \Aimeos\MShop\Common\Item\ListsRef\Iface $item Item with referenced items
	 * @param string $domain Domain of the calling manager
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\ListsRef\Iface $item with updated referenced items
	 */
	protected function saveListItems( \Aimeos\MShop\Common\Item\ListsRef\Iface $item, string $domain,
		bool $fetch = true ) : \Aimeos\MShop\Common\Item\ListsRef\Iface
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

				$refManager[$refDomain]->delete( $ids );
			}

			$listManager->delete( $rmListIds );


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

					$refItem = $refManager[$refDomain]->save( $refItem );
					$listItem->setRefId( $refItem->getId() );
				}

				if( $listItem->getParentId() != $item->getId() ) {
					$listItem->setId( null ); // create new list item if copied
				}

				$listManager->save( $listItem->setParentId( $item->getId() ), $fetch );
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
