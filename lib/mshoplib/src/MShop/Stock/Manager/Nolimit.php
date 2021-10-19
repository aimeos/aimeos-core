<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018-2021
 * @package MShop
 * @subpackage Stock
 */


namespace Aimeos\MShop\Stock\Manager;


/**
 * Stock manager implementation for unlimited stocks
 *
 * @package MShop
 * @subpackage Stock
 */
class Nolimit
	extends \Aimeos\MShop\Stock\Manager\Standard
	implements \Aimeos\MShop\Stock\Manager\Iface, \Aimeos\MShop\Common\Manager\Factory\Iface
{
	/**
	 * Removes old entries from the storage.
	 *
	 * @param iterable $siteids List of IDs for sites whose entries should be deleted
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function clear( iterable $siteids ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Inserts the new stock item
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface $item Stock item which should be saved
	 * @param bool $fetch True if the new ID should be returned in the item
	 * @return \Aimeos\MShop\Stock\Item\Iface Updated item including the generated ID
	 */
	public function saveItem( \Aimeos\MShop\Stock\Item\Iface $item, bool $fetch = true ) : \Aimeos\MShop\Stock\Item\Iface
	{
		return $item;
	}


	/**
	 * Removes multiple items.
	 *
	 * @param \Aimeos\MShop\Common\Item\Iface[]|string[] $itemIds List of item objects or IDs of the items
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function delete( $itemIds ) : \Aimeos\MShop\Common\Manager\Iface
	{
		return $this;
	}


	/**
	 * Creates a stock item object for the given item id.
	 *
	 * @param string $id Id of the stock item
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param bool|null $default Add default criteria or NULL for relaxed default criteria
	 * @return \Aimeos\MShop\Stock\Item\Iface Returns the product stock item of the given id
	 * @throws \Aimeos\MShop\Exception If item couldn't be found
	 */
	public function get( string $id, array $ref = [], ?bool $default = false ) : \Aimeos\MShop\Common\Item\Iface
	{
		$values = ['stock.id' => $id, 'stock.type' => 'default'];
		return $this->getObject()->create( $values );
	}


	/**
	 * Search for stock items based on the given critera.
	 *
	 * @param \Aimeos\MW\Criteria\Iface $search Search criteria object
	 * @param string[] $ref List of domains to fetch list items and referenced items for
	 * @param int|null &$total Number of items that are available in total
	 * @return \Aimeos\Map List of items implementing \Aimeos\MShop\Stock\Item\Iface with ids as keys
	 */
	public function search( \Aimeos\MW\Criteria\Iface $search, array $ref = [], int &$total = null ) : \Aimeos\Map
	{
		$items = [];
		$item = $this->getObject()->create( ['stock.type' => 'default'] );

		foreach( $this->getProductIds( $search->getConditions() ) as $idx => $prodid ) {
			$items[$idx] = ( clone $item )->setProductId( $prodid )->setId( $idx );
		}

		if( $total !== null ) {
			$total = count( $items );
		}

		return map( array_splice( $items, 0, $search->getLimit() ) );
	}


	/**
	 * Decreases the stock level for the given product ID/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product IDs as keys and quantities as values
	 * @param string $type Unique code of the stock type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function decrease( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface
	{
		return $this;
	}


	/**
	 * Increases the stock level for the given product ID/quantity pairs and type
	 *
	 * @param array $pairs Associative list of product IDs as keys and quantities as values
	 * @param string $type Unique code of the type
	 * @return \Aimeos\MShop\Stock\Manager\Iface Manager object for chaining method calls
	 */
	public function increase( iterable $pairs, string $type = 'default' ) : \Aimeos\MShop\Stock\Manager\Iface
	{
		return $this;
	}


	/**
	 * Returns the product IDs from the conditions
	 *
	 * @param \Aimeos\MW\Criteria\Expression\Iface|null $cond Criteria object
	 * @return string[] List of product IDs
	 */
	protected function getProductIds( \Aimeos\MW\Criteria\Expression\Iface $cond = null ) : array
	{
		$list = [];

		if( $cond instanceof \Aimeos\MW\Criteria\Expression\Combine\Iface )
		{
			foreach( $cond->getExpressions() as $expr ) {
				$list = array_merge( $list, $this->getProductIds( $expr ) );
			}
		}
		elseif( $cond instanceof \Aimeos\MW\Criteria\Expression\Compare\Iface )
		{
			if( $cond->getName() === 'stock.productid' && $cond->getOperator() === '==' ) {
				$list = array_merge( $list, (array) $cond->getValue() );
			}
		}

		return $list;
	}
}
