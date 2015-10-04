<?php

/**
 * @copyright Copyright (c) Metaways Infosystems GmbH, 2014
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @package MShop
 * @subpackage Common
 */


/**
 * Abstract class for managers working with referenced list items.
 *
 * @package MShop
 * @subpackage Common
 */
abstract class MShop_Common_Manager_ListRef_Base
	extends MShop_Common_Manager_Base
	implements MShop_Common_Manager_ListRef_Iface
{
	/**
	 * Creates a new item for the specific manager.
	 *
	 * @param array $values Associative list of key/value pairs
	 * @param array $listItems List of items implementing MShop_Common_Item_Lists_Iface
	 * @param array $refItems List of items implementing MShop_Common_Item_Iface
	 * @return MShop_Common_Item_Iface New item
	 */
	abstract protected function createItemBase( array $values = array(), array $listItems = array(), array $refItems = array() );


	/**
	 * Creates the items with address item, list items and referenced items.
	 *
	 * @param array $map Associative list of IDs as keys and the associative array of values
	 * @param array $domains List of domains to fetch list items and referenced items for
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_Item_Iface
	 */
	protected function buildItems( array $map, array $domains, $prefix )
	{
		$items = $listItemMap = $refItemMap = $refIdMap = array();

		if( count( $domains ) > 0 )
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
			$listItems = array();
			if( isset( $listItemMap[$id] ) ) {
				$listItems = $listItemMap[$id];
			}

			$refItems = array();
			if( isset( $refItemMap[$id] ) ) {
				$refItems = $refItemMap[$id];
			}

			$items[$id] = $this->createItemBase( $values, $listItems, $refItems );
		}

		return $items;
	}


	/**
	 * Returns the list items that belong to the given IDs.
	 *
	 * @param array $ids List of IDs
	 * @param array $domains List of domain names whose referenced items should be attached
	 * @param string $prefix Domain prefix
	 * @return array List of items implementing MShop_Common_Lists_Item_Iface
	 */
	protected function getListItems( array $ids, array $domains, $prefix )
	{
		$manager = $this->getSubManager( 'lists' );

		$search = $manager->createSearch( true );

		$expr = array(
			$search->compare( '==', $prefix . '.lists.parentid', $ids ),
			$search->compare( '==', $prefix . '.lists.domain', $domains ),
			$search->getConditions(),
		);

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
		$items = array();
		$context = $this->getContext();

		foreach( $refIdMap as $domain => $list )
		{
			try
			{
				$manager = MShop_Factory::createManager( $context, $domain );

				$search = $manager->createSearch( true );
				$expr = array(
					$search->compare( '==', str_replace( '/', '.', $domain ) . '.id', array_keys( $list ) ),
					$search->getConditions(),
				);
				$search->setConditions( $search->combine( '&&', $expr ) );
				$search->setSlice( 0, 0x7fffffff );

				foreach( $manager->searchItems( $search ) as $id => $item )
				{
					foreach( $list[$id] as $parentId ) {
						$items[$parentId][$domain][$id] = $item;
					}
				}
			}
			catch( MShop_Exception $e )
			{
				$logger = $context->getLogger();
				$logger->log( sprintf( 'Item referenced in domain "%1$s" not found: %2$s', $domain, $e->getMessage() ) );
				$logger->log( $e->getTraceAsString() );
			}
		}

		return $items;
	}
}
