<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2024-2025
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Manager\Decorator;


/**
 * Provides a decorator for managing stock items
 *
 * @package MShop
 * @subpackage Product
 */
class Stock
	extends \Aimeos\MShop\Common\Manager\Decorator\Base
	implements \Aimeos\MShop\Common\Manager\Iface
{
	/**
	 * Creates a new stock item object
	 *
	 * @param array $values Values the item should be initialized with
	 * @return \Aimeos\MShop\Stock\Item\Iface New stock item object
	 */
	public function createStockItem( array $values = [] ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return \Aimeos\MShop::create( $this->context(), 'stock' )->create( $values );
	}


	/**
	 * Creates objects from the given array
	 *
	 * @param iterable $entries List of associative arrays with key/value pairs
	 * @param array $refs List of domains to retrieve list items and referenced items for
	 * @param array $excludes List of keys which shouldn't be used when creating the items
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Common\Item\Iface
	 */
	public function from( iterable $entries, array $refs = [], array $excludes = [] ) : \Aimeos\Map
	{
		$keys = array_flip( $excludes );
		$excludes[] = 'stock';

		$items = $this->getManager()->from( $entries, $refs, $excludes );

		foreach( $entries as $key => $entry )
		{
			if( isset( $entry['stock'] ) && ( $item = $items->get( $key ) ) )
			{
				foreach( $entry['stock'] as $list )
				{
					$list = array_diff_key( $list, $keys );
					$item->addStockItem( $this->createStockItem()->fromArray( $list, true ) );
				}
			}
		}

		return $items;
	}


	/**
	 * Saves the dependent items of the item
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface $item Item object
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Common\Item\Iface Updated item
	 */
	public function saveRefs( \Aimeos\MShop\Common\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Common\Item\Iface
	{
		$this->saveStockItems( $item, $fetch );

		return $this->getManager()->saveRefs( $item );
	}


	/**
	 * Merges the data from the given map and the referenced items
	 *
	 * @param array $entries Associative list of ID as key and the associative list of stock key/value pairs as values
	 * @param array $ref List of referenced items to fetch and add to the entries
	 * @return array Associative list of ID as key and the updated entries as value
	 */
	public function searchRefs( array $entries, array $ref ) : array
	{
		$entries = $this->getManager()->searchRefs( $entries, $ref );

		if( $this->hasRef( $ref, 'stock' ) )
		{
			foreach( $this->getStockItems( array_keys( $entries ), $ref ) as $id => $list ) {
				$entries[$id]['.stock'] = $list;
			}
		}

		return $entries;
	}


	/**
	 * Returns the stock items for the given parent IDs
	 *
	 * @param string[] $prodIds List of parent IDs
	 * @param array|null $ref Referenced items that should be fetched too
	 * @return array Associative list of parent IDs / stock IDs as keys and items implementing
	 * 	\Aimeos\MShop\Stock\Item\Iface as values
	 */
	protected function getStockItems( array $prodIds, ?array $ref = [] ) : array
	{
		if( empty( $prodIds ) ) {
			return [];
		}

		$manager = \Aimeos\MShop::create( $this->context(), 'stock' );
		$filter = $manager->filter()->slice( 0, 0x7fffffff )->add( 'stock.productid', '==', $prodIds );

		$types = $ref && isset( $ref['stock'] ) && is_array( $ref['stock'] ) ? $ref['stock'] : null;

		if( !empty( $types ) ) {
			$filter->add( 'stock.type', '==', $types );
		}

		return $manager->search( $filter, $ref ?? [] )->groupBy( 'stock.productid' )->all();
	}


	/**
	 * Adds new, updates existing and deletes removed stock items
	 *
	 * @param \Aimeos\MShop\Product\Item\Iface $item Item with stock items
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Product\Item\Iface Item with saved stock items
	 */
	protected function saveStockItems( \Aimeos\MShop\Product\Item\Iface $item,
		bool $fetch = true ) : \Aimeos\MShop\Product\Item\Iface
	{
		$stockManager = \Aimeos\MShop::create( $this->context(), 'stock' );
		$stockManager->delete( $item->getStockItemsDeleted() );

		$stockItems = $item->getStockItems( null, false );

		foreach( $stockItems as $stockItem )
		{
			if( $stockItem->getProductId() != $item->getId() ) {
				$stockItem->setId( null ); // create new stock item if copied
			}

			$stockItem->setProductId( $item->getId() );
		}

		$stockManager->save( $stockItems, $fetch );
		return $item;
	}
}
