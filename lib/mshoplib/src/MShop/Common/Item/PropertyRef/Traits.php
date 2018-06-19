<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2018
 * @package MShop
 * @subpackage Common
 */


namespace Aimeos\MShop\Common\Item\PropertyRef;


/**
 * Common trait for items containing property items
 *
 * @package MShop
 * @subpackage Common
 */
trait Traits
{
	private $propItems = [];
	private $propRmItems = [];
	private $propMax = 0;


	/**
	 * Adds a new property item or overwrite an existing one
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item New or existing property item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 */
	public function addPropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item )
	{
		$id = $item->getId() ?: 'id-' . $this->propMax++;
		$this->propItems[$id] = $item;

		return $this;
	}


	/**
	 * Removes an existing property item
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface $item Existing property item
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If given property item isn't found
	 */
	public function deletePropertyItem( \Aimeos\MShop\Common\Item\Property\Iface $item )
	{
		foreach( $this->propItems as $key => $pitem )
		{
			if( $pitem === $item )
			{
				$this->propRmItems[$item->getId()] = $item;
				unset( $this->propItems[$key] );

				return $this;
			}
		}

		throw new \Aimeos\MShop\Exception( sprintf( 'Property item for removal not found' ) );
	}


	/**
	 * Removes a list of existing property items
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $items Existing property items
	 * @return \Aimeos\MShop\Common\Item\Iface Self object for method chaining
	 * @throws \Aimeos\MShop\Exception If an item isn't a property item or isn't found
	 */
	public function deletePropertyItems( array $items )
	{
		foreach( $items as $item )
		{
			if( !( $item instanceof \Aimeos\MShop\Common\Item\Property\Iface ) ) {
				throw new \Aimeos\MShop\Exception( sprintf( 'Not a property item' ) );
			}

			$this->deletePropertyItem( $item );
		}

		return $this;
	}


	/**
	 * Returns the deleted property items
	 *
	 * @return \Aimeos\MShop\Common\Item\Property\Iface[] Property items
	 */
	public function getPropertyItemsDeleted()
	{
		return $this->propRmItems;
	}


	/**
	 * Returns the property items of the product
	 *
	 * @param array|string|null $type Name of the property item type or null for all
	 * @param boolean $active True to return only active items, false to return all
	 * @return \Aimeos\MShop\Common\Item\Property\Iface[] Associative list of property IDs as keys and property items as values
	 */
	public function getPropertyItems( $type = null, $active = true )
	{
		$list = [];

		foreach( $this->propItems as $propId => $propItem )
		{
			if( ( $type === null || in_array( $propItem->getType(), (array) $type ) )
				&& ( $active === false || $propItem->isAvailable() )
			) {
				$list[$propId] = $propItem;
			}
		}

		return $list;
	}


	/**
	 * Sets the property items in the trait
	 *
	 * @param \Aimeos\MShop\Common\Item\Property\Iface[] $items Property items
	 */
	protected function setPropertyItems( array $items )
	{
		$this->propItems = $items;
	}
}
