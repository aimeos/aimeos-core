<?php

/**
 * @license LGPLv3, https://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2025
 * @package MShop
 * @subpackage Product
 */


namespace Aimeos\MShop\Product\Item;


/**
 * Stock trait for products containing stock items
 *
 * @package MShop
 * @subpackage Product
 */
trait Stock
{
	private array $stockItems = [];
	private array $stockRmItems = [];


	/**
	 * Returns the unique ID of the item.
	 *
	 * @return string|null ID of the item
	 */
	abstract public function getId() : ?string;


	/**
	 * Creates a deep clone of all objects
	 */
	public function __clone()
	{
		parent::__clone();

		foreach( $this->stockItems as $key => $item ) {
			$this->stockItems[$key] = clone $item;
		}

		foreach( $this->stockRmItems as $key => $item ) {
			$this->stockRmItems[$key] = clone $item;
		}
	}


	/**
	 * Adds a new stock item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
	 * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
	 */
	public function addStockItem( \Aimeos\MShop\Stock\Item\Iface $item ) : \Aimeos\MShop\Product\Item\Iface
	{
		$id = $item->getId() ?: '_' . $this->getId() . '_' . $item->getType();
		$this->stockItems[$id] = $item;

		return $this;
	}


	/**
	 * Adds new stock items or overwrite existing ones
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface $item New or existing stock item
	 * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
	 */
	public function addStockItems( iterable $items ) : \Aimeos\MShop\Product\Item\Iface
	{
		foreach( $items as $item ) {
			$this->addStockItem( $item );
		}

		return $this;
	}


	/**
	 * Removes an existing stock item
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface $item Existing stock item
	 * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
	 */
	public function deleteStockItem( \Aimeos\MShop\Stock\Item\Iface $item ) : \Aimeos\MShop\Product\Item\Iface
	{
		$id = $item->getId();

		if( isset( $this->stockItems[$id] ) )
		{
			$this->stockRmItems[$id] = $item;
			unset( $this->stockItems[$id] );
			return $this;
		}

		$id = '_' . $this->getId() . '_' . $item->getType();

		if( isset( $this->stockItems[$id] ) )
		{
			$this->stockRmItems[$id] = $item;
			unset( $this->stockItems[$id] );
		}

		return $this;
	}


	/**
	 * Removes a list of existing stock items
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items Existing stock items
	 * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
	 */
	public function deleteStockItems( iterable $items ) : \Aimeos\MShop\Product\Item\Iface
	{
		foreach( $items as $item ) {
			$this->deleteStockItem( $item );
		}

		return $this;
	}


	/**
	 * Returns the deleted stock items
	 *
	 * @return \Aimeos\Map Stock items implementing \Aimeos\MShop\Stock\Item\Iface
	 */
	public function getStockItemsDeleted() : \Aimeos\Map
	{
		return map( $this->stockRmItems );
	}


	/**
	 * Returns the stock items associated to the product
	 *
	 * @param array|string|null $type Type or types of the stock item
	 * @return \Aimeos\Map Associative list of items implementing \Aimeos\MShop\Stock\Item\Iface
	 */
	public function getStockItems( $type = null ) : \Aimeos\Map
	{
		$list = map( $this->stockItems );

		if( $type !== null )
		{
			$list = $list->filter( function( $item ) use ( $type ) {
				return in_array( $item->getType(), (array) $type, true );
			});
		}

		return $list;
	}


	/**
	 * Adds a new stock item or overwrite an existing one
	 *
	 * @param \Aimeos\Map|\Aimeos\MShop\Stock\Item\Iface[] $items New list of stock items
	 * @return \Aimeos\MShop\Product\Item\Iface Self object for method chaining
	 */
	public function setStockItems( iterable $items ) : \Aimeos\MShop\Product\Item\Iface
	{
		$list = [];

		foreach( $items as $p )
		{
			$id = $p->getId() ?: '_' . $this->getId() . '_' . $p->getType();
			unset( $this->stockItems[$id] );
			$list[$id] = $p;
		}

		$this->deleteStockItems( $this->stockItems );
		$this->stockItems = $list;

		return $this;
	}


	/**
	 * Sets the stock items in the trait
	 *
	 * @param \Aimeos\MShop\Stock\Item\Iface[] $items Stock items
	 */
	protected function initStockItems( array $items )
	{
		$this->stockItems = $items;
	}
}
